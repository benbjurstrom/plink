<x-mail::message>
# Hello!

Click the button below to securely log in to your account:

<x-mail::button :url="$url">
Sign-In to {{ config('app.name') }}
</x-mail::button>

This link expires after 5 minutes and can only be used once.

Thank you for using {{ config('app.name') }}!

<x-mail::subcopy>
If you're having trouble clicking the "Sign-In to {{ config('app.name') }}" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
</x-mail::subcopy>
</x-mail::message>
