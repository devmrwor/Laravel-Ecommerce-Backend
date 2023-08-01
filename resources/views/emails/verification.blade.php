<x-mail::message>
# Reset Password

Click the button below to reset the password.

<x-mail::button :url="$url">
Reset Password
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
