<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Registration;
use App\Models\RegistrationDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    use WithFileUploads;

    // In real application, this should be stored in database
    public array $registers = [];
    public string $registranName = '';
    public string $registranCompany = '';
    public string $registranEmail = '';
    public string $registranPhoneNumber = '';
    public string $registranWhatsappNumber = '';
    public string $registranWeChatNumber = '';

    public bool $is_show_inquiry = true;

    public $wordMarks = [
        '',
    ];

    #[Validate('image|max:1024')]
    public $logoMark;

    public $goodsOrServices = null;
    public $quotationCurrency = "IDR - Indonesian Rupiah";
    public $trademarkAdminType = "Madrid Protocol";
    public $selectedCountries = [];
    public $selectedClasses = [];

    public function addWordMark()
    {
        // if last item in wordMarks is empty, do not add new item
        if (end($this->wordMarks) === '') {
            return;
        }

        $this->wordMarks[] = '';
    }

    public function removeWordMark($index)
    {
        unset($this->wordMarks[$index]);
        $this->wordMarks = array_values($this->wordMarks); // Reindex the array
    }

    public array $regions;
    public array $classes;

    public array $accordions = [];

    public function mount()
    {
        $this->regions = config('form-fields.regions');
        $this->classes = config('form-fields.classes');


        $this->generateAccordion();
    }

    function generateAccordion() {

        foreach ($this->regions as $key => $value) {
            $this->accordions[] = [
                'title' => $value['name'],
                'open' => false,
                'key' => $key,
            ];
        };
    }

    public function storeFields()
    {
        $this->fill([
            'registers' => array_merge($this->registers, [[
                'wordMarks' => $this->wordMarks,
                'logoMark' => $this->logoMark,
                'goodsOrServices' => $this->goodsOrServices,
                'quotationCurrency' => $this->quotationCurrency,
                'trademarkAdminType' => $this->trademarkAdminType,
                'selectedCountries' => $this->selectedCountries,
                'selectedClasses' => $this->selectedClasses,
            ]]),
        ]);

        // Reset form fields after storing
        $this->reset([
            'wordMarks',
            'logoMark',
            'goodsOrServices',
            'quotationCurrency',
            'trademarkAdminType',
            'selectedCountries',
            'selectedClasses',
        ]);

        $this->wordMarks = [''];
        $this->is_show_inquiry = false;
    }

    public function showInquiry()
    {
        $this->is_show_inquiry = true;

        // Scroll to top after showing the inquiry form
        $this->js("window.scrollTo({ top: 0, behavior: 'smooth' });");
    }

    public function loadFields($index)
    {
        $register = $this->registers[$index];

        $this->wordMarks = $register['wordMarks'];
        $this->logoMark = $register['logoMark'];
        $this->goodsOrServices = $register['goodsOrServices'];
        $this->quotationCurrency = $register['quotationCurrency'];
        $this->trademarkAdminType = $register['trademarkAdminType'];
        $this->selectedCountries = $register['selectedCountries'];
        $this->selectedClasses = $register['selectedClasses'];
    }

    public function sendEnquiry()
    {
        $fieldRegisters                 = $this->registers;
        $fieldRegistranName             = $this->registranName;
        $fieldRegistranCompany          = $this->registranCompany;
        $fieldRegistranEmail            = $this->registranEmail;
        $fieldRegistranPhoneNumber      = $this->registranPhoneNumber;
        $fieldRegistranWhatsappNumber   = $this->registranWhatsappNumber;
        $fieldRegistranWeChatNumber     = $this->registranWeChatNumber;

        DB::beginTransaction();
        try {
            $register = Registration::create([
                'name'      => $fieldRegistranName,
                'email'     => $fieldRegistranEmail,
                'phone'     => $fieldRegistranPhoneNumber,
                'company'   => $fieldRegistranCompany,
                'whatsapp'  => $fieldRegistranWhatsappNumber,
                'wechat'    => $fieldRegistranWeChatNumber,
            ]);

            $logoPrefix = 'registrations/' . $register->id . '/logos/';

            foreach ($fieldRegisters as $item) {

                $logoPath = null;
                if ($item['logoMark']) {
                    $logoPath = $item['logoMark']->store($logoPrefix, 'public');
                }

                $register->details()->create([
                    'word_marks'                => $item['wordMarks'] ? implode(', ', $item['wordMarks']) : null,
                    'logo'                      => $logoPath,
                    'goods_services'            => $item['goodsOrServices'],
                    'currency'                  => $item['quotationCurrency'],
                    'trademark_administration'  => $item['trademarkAdminType'],
                    'countries'                 => $item['selectedCountries'] ? implode(', ', $item['selectedCountries']) : null,
                    'classifications'           => $item['selectedClasses'] ? implode(', ', $item['selectedClasses']) : null,
                ]);
            }

        } catch (\Throwable $th) {
            Log::error('Failed to submit enquiry', [
                'error' => $th->getMessage(),
            ]);
            DB::rollBack();
            $this->js("alert('Failed to submit enquiry. Please try again later.');");
        }

        DB::commit();

        Log::info('Enquiry submitted', [
            'registers' => $fieldRegisters,
            'registranName' => $fieldRegistranName,
            'registranCompany' => $fieldRegistranCompany,
            'registranEmail' => $fieldRegistranEmail,
            'registranPhoneNumber' => $fieldRegistranPhoneNumber,
            'registranWhatsappNumber' => $fieldRegistranWhatsappNumber,
            'registranWeChatNumber' => $fieldRegistranWeChatNumber,
        ]);

        // In real application, this should send the enquiry to the backend and store in database
        // For this example, we will just reset the form fields and show a success message
        $this->reset([
            'registranName',
            'registranCompany',
            'registranEmail',
            'registranPhoneNumber',
            'registranWhatsappNumber',
            'registranWeChatNumber',
        ]);
        $this->registers = [];
        $this->js("alert('Enquiry submitted successfully!');");
    }
};
?>

<div>
    {{-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant --}}
    <div class="h-32"></div>

    <section class="mx-auto max-w-6xl">
        <h1 class="flex flex-col my-8 font-bold text-3xl">
            <span>ENQUIRY</span>
            <span class="bg-orange-300 mt-2 w-full max-w-32 h-1"></span>
        </h1>

        <h2 class="mb-4 font-bold text-3xl">TRADEMARK INFORMATION</h2>

        <div x-data="{ is_show_inquiry: @entangle('is_show_inquiry') }" class="mb-6">
            <div
                x-show="is_show_inquiry"
                class="bg-base-200 card-border rounded-2xl card card-xl">
                <div class="card-body">
                    <div class="space-y-6">
                        <fieldset class="fieldset">
                            <legend class="font-bold text-lg fieldset-legend">Word Mark</legend>
                            @foreach ($wordMarks as $index => $item)
                                <div
                                    class="relative">
                                    <input
                                        wire:model.live="wordMarks.{{ $index }}"
                                        type="text" class="rounded-full w-full input" placeholder="Type here" />

                                    @if ($index != 0)
                                        <div
                                            wire:click="removeWordMark({{ $index }})"
                                            class="right-2 absolute inset-y-1/2 bg-error rounded-full w-min h-min overflow-hidden -translate-y-1/2 cursor-pointer">
                                            <span class="block bg-error p-1">
                                                <x-heroicon-o-trash class="size-4 text-white"/>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            <button wire:click="addWordMark" class="bg-success rounded-full text-white btn btn-md">Add Word Mark</button>

                            <p class="label"><i>for a Word Mark, enter the text (eg. TOYOTA, PROTON); seperate word mark with comma if you have more than one wordmark</i></p>
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="font-bold text-lg fieldset-legend">Logo/Device Mark</legend>
                            <input type="file"
                                wire:model.live="logoMark"
                                class="rounded-full w-full file-input file-input-neutral" />
                            <label class="label"><i>for a Device Mark (logo), upload your file. (*.jpg, *.gif, *.png format only)</i></label>
                            @if ($logoMark)
                                <img src="{{ $logoMark->temporaryUrl() }}" class="w-32 h-32 object-contain" alt="Logo Mark Preview">
                            @endif
                        </fieldset>

                        <div class="fieldset">
                            <label for="" class="font-bold text-lg">Classes</label>
                            <div
                                x-data="{ selectedClasses: @entangle('selectedClasses') }"
                            >

                                <div class=""
                                    x-show="selectedClasses.length > 0">
                                    <div class="mb-12 overflow-x-auto">
                                        <table class="table table-zebra">
                                            <!-- head -->
                                            <thead>
                                            <tr>
                                                <th class="w-36">Class</th>
                                                <th>Description</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($selectedClasses as $item)
                                                <tr>
                                                    <th class="text-center">{{ $item }}</th>
                                                    <td>{{ $classes[$item] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <button
                                    x-on:click="document.getElementById('my_modal_1').showModal()"
                                    class="bg-success rounded-full text-white btn btn-md">Select Classes</button>

                                <dialog
                                    wire:ignore
                                    id="my_modal_1" class="modal">
                                    <div
                                        class="rounded-2xl w-full max-w-5xl modal-box">
                                        <h3 class="font-bold text-lg">SELECT CLASSES FOR “TRADEMARK”</h3>
                                        <p class="mb-6"><i>Select the relevant classification of goods and services to the above trade mark application.
                                            You may select one or more classes which are applicable to your trademark application.</i></p>



                                        <div class="flex flex-col space-y-4 w-full">
                                            @foreach ($classes as $key => $item)
                                                <label class="whitespace-normal label label-lg">
                                                    <input
                                                        wire:model.live="selectedClasses"
                                                        value="{{ $key }}"
                                                        type="checkbox"
                                                        class="checkbox checkbox-lg" />
                                                    <p class="text-primary-content text-lg"><strong>{{ $key }}</strong> - {{ $item }}</p>
                                                </label>
                                            @endforeach
                                        </div>

                                        <div class="modal-action">
                                            <form method="dialog">
                                                <!-- if there is a button in form, it will close the modal -->
                                                <button class="btn">Close</button>
                                            </form>
                                        </div>
                                    </div>
                                </dialog>
                            </div>
                        </div>

                        {{-- <fieldset class="fieldset">
                            <legend class="font-bold text-lg fieldset-legend">Items of the Goods or Services</legend>
                            <p class="label"><i>If you unsure the class(es). Please key in the goods or services below. <br> We will advice you on the relevant class(es) to be applied for under your trade mark application.</i></p>
                            <textarea wire:model.live="goodsOrServices" class="rounded-2xl w-full textarea" rows="4" placeholder="Type here"></textarea>
                        </fieldset> --}}

                        <fieldset class="fieldset">
                            <legend class="font-bold text-lg fieldset-legend">Quotation Currency</legend>
                            <select wire:model.live="quotationCurrency" class="rounded-full w-full select-neutral select">
                                <option value="IDR - Indonesian Rupiah">- IDR - Indonesian Rupiah</option>
                                <option value="USD - United States Dollar">- USD - United States Dollar</option>
                            </select>
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="font-bold text-lg fieldset-legend">Trademark Administration Type</legend>
                            <div class="flex gap-4">
                                <label class="label">
                                    <input type="radio" name="trademark_admin_type" class="radio" wire:model.live="trademarkAdminType" value="Madrid Protocol" checked="checked" />
                                    <div class="flex flex-col">
                                        <span>
                                            Madrid Protocol
                                        </span>
                                        <span>
                                            min. 5 countries
                                        </span>
                                    </div>
                                </label>

                                <label class="label">
                                    <input type="radio" name="trademark_admin_type" class="radio" wire:model.live="trademarkAdminType" value="Paris Convention (Conventional Application)" />
                                    Paris Convention (Conventional Application)
                                </label>
                            </div>

                        </fieldset>

                        <div
                            wire:ignore.self
                            x-data="{ dropdownData: @entangle('accordions') }"
                            class="bg-base-100 w-full join join-vertical">
                            @foreach ($accordions as $accordion)
                                <div
                                    wire:ignore.self
                                    :class="dropdownData[{{ $loop->index }}].open ? 'collapse-open' : ''"
                                    class="collapse collapse-arrow border border-base-300 join-item">
                                    <div
                                        @click="dropdownData[{{ $loop->index }}].open = !dropdownData[{{ $loop->index }}].open"
                                        class="collapse-title font-semibold">{{ $accordion['title'] }}</div>

                                    <div class="collapse-content">
                                        <div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($regions[$accordion['key']]['countries'] as $country)
                                                <label class="whitespace-normal label">
                                                    <input
                                                        wire:model.live="selectedCountries"
                                                        value="{{ $country }}"
                                                        type="checkbox"
                                                        class="checkbox checkbox-lg" />
                                                    {{ $country }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button wire:click="storeFields" class="bg-success rounded-full text-white btn btn-md">Submit Information</button>
                    </div>
                </div>
            </div>

        </div>




        <div class="bg-base-200 mt-12 card-border rounded-2xl card card-xl">

                <div class="card-body">


                    @if (count($registers) == 0)
                    <div class="flex flex-col">
                        <p class="w-full font-bold text-warning text-center">No Enquiry Yet</p>

                        {{-- Add button --}}
                        <div class="flex justify-center mt-6">
                            <button
                                wire:click="$set('is_show_inquiry', true)"
                                class="bg-success rounded-full text-white btn btn-md">Make an Enquiry</button>
                        </div>
                    </div>

                    @else
                    <div class="flex flex-col space-y-6">
                        <div class="flex flex-wrap justify-start gap-4">
                            @foreach ($registers as $index => $item)
                                <div class="bg-base-100 shadow-sm w-96 card">
                                    <div class="w-full aspect-square">
                                        <img
                                            class="w-full h-full object-contain"
                                            src="{{ $item['logoMark'] ? $item['logoMark']->temporaryUrl() : '' }}"/>
                                    </div>
                                    <div class="items-center text-center card-body">
                                        <h2 class="card-title">{{ $item['wordMarks'] ? implode(', ', $item['wordMarks']) : 'No Word Mark' }}</h2>
                                        <div class="card-actions">
                                            <button wire:click="loadFields({{ $index }})" class="bg-success rounded-full text-white btn btn-sm">See Details</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Button to make a new enquiry, and scroll to top --}}
                        <div class="flex justify-center mt-6">
                            <button
                                wire:click="showInquiry"
                                class="bg-success rounded-full text-white btn btn-md">Make another trademark information enquiry</button>
                        </div>

                    </div>
                    @endif
                </div>

        </div>

        <div class="bg-base-200 mt-12 card-border rounded-2xl card card-xl">
            <div class="card-body">
                <p class="mt-4">Please check whether the data summary for registration are all correct before submitting the enquiry. Please also fill in a valid contact information for us to be able to follow up the enquiry.</p>

                {{-- Name*
                Company
                Email* (can be input multiple emails separate by comma)
                Active Phone Number*
                Whatsapp Number (if available)
                WeChat Number (if available) --}}
                <div class="space-y-6">

                    {{-- Form Fields --}}
                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
                        <div class="w-full form-control">
                            <label class="label">
                                <span class="font-bold label-text">Name*</span>
                            </label>
                            <input
                                wire:model.live="registranName"
                                type="text" placeholder="Type here" class="rounded-full w-full input" />
                        </div>

                        <div class="w-full form-control">
                            <label class="label">
                                <span class="font-bold label-text">Company</span>
                            </label>
                            <input
                                wire:model.live="registranCompany"
                                type="text" placeholder="Type here" class="rounded-full w-full input" />
                        </div>

                        <div class="w-full form-control">
                            <label class="label">
                                <span class="font-bold label-text">Email*</span>
                            </label>
                            <input
                                wire:model.live="registranEmail"
                                type="text" placeholder="Type here" class="rounded-full w-full input" />
                        </div>

                        <div class="w-full form-control">
                            <label class="label">
                                <span class="font-bold label-text">Active Phone Number*</span>
                            </label>
                            <input
                                wire:model.live="registranPhoneNumber"
                                type="text" placeholder="Type here" class="rounded-full w-full input" />
                        </div>

                        <div class="w-full form-control">
                            <label class="label">
                                <span class="font-bold label-text">Whatsapp Number</span>
                            </label>
                            <input
                                wire:model.live="registranWhatsappNumber"
                                type="text" placeholder="Type here" class="rounded-full w-full input" />
                        </div>

                        <div class="w-full form-control">
                            <label class="label">
                                <span class="font-bold label-text">WeChat Number</span>
                            </label>
                            <input
                                wire:model.live="registranWeChatNumber"
                                type="text" placeholder="Type here" class="rounded-full w-full input" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button
                            wire:click="sendEnquiry"
                            class="bg-success rounded-full text-white btn btn-md">Submit Enquiry</button>
                    </div>

                </div>
            </div>

        </div>

    </section>

    <div class="h-32"></div>
</div>
