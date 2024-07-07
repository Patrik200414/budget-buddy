<x-mail::message>
# Reset Password

You can reset your password through this link. The link will be alive for 30 minutes!

<x-mail::button :url="$url">
Reset Password!
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
