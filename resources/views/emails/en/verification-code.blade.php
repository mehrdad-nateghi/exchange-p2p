<x-mail::message>
# Mail Verification

سلام
<h2>{{ $verificationCode }}</h2>

<p style="margin-top: 20px;">Thanks,<br>
    <strong>{{ config('app.name') }}</strong></p>
</x-mail::message>

