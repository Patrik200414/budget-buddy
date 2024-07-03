<x-mail::message>
# Registration Verification

To validate your registration you should click on the Verify button.

<x-mail::button :url="''">
Verify!
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
