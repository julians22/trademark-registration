<?php

use Livewire\Component;

new class extends Component
{
    public $wordMarks = [];

    public function render()
    {
        return view('components.fields.word-mark');
    }

    public function addWordMark()
    {
        $this->wordMarks[] = '';
    }
};
?>

<div>
    <fieldset class="fieldset">
        <legend class="font-bold text-lg fieldset-legend">Word Mark</legend>
        @foreach ($wordMarks as $index => $item)
            <input
                wire:model.live="wordMarks.{{ $index }}"
                type="text" class="rounded-full w-full input" placeholder="Type here" />
        @endforeach
        <button wire:click="addWordMark" class="bg-success rounded-full text-white btn btn-md">Add Word Mark</button>

        <p class="label"><i>for a Word Mark, enter the text (eg. TOYOTA, PROTON); seperate word mark with comma if you have more than one wordmark</i></p>
    </fieldset>
</div>
