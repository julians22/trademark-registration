<x-mail::message>
# Registration Created

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

We will review the registration and get back to you shortly.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
