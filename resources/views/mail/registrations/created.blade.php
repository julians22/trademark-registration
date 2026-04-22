<x-mail::message>
# New Registration Created

A new registration has been created with the following details:

<x-mail::table>
    | Field       | Value           |
    | ----------- | --------------- |
    | ***Name***        | {{ $registration->name }} |
    | ***Email***       | {{ $registration->email }} |
    | ***Phone***       | {{ $registration->phone }} |
    | ***Trademark***   | {{ $registration->trademark }} |
    | ***Created At***  | {{ $registration->created_at->format('Y-m-d H:i:s') }} |
</x-mail::table>

Check the admin panel for more details.
Click the button below to view the registration.

<x-mail::button :url="route('filament.admin.resources.registrations.view', $registration->id)">
View Registration
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
