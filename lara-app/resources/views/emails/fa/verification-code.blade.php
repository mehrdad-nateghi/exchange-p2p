@extends('emails.layouts.master')

@section('content')
    <p>ممنون که در {{ $appName }} حساب کاربری ایجاد کردید.</p>
    <p>کد تأیید شما برای تکمیل ثبت ‌نام:</p>
    <p>{{ $verificationCode }}</p>
    <p>این کد به مدت {{ $verificationCodeExpirationTimePerMinutes }} دقیقه معتبر است. لطفاً این کد را با کسی به اشتراک نگذارید.</p>
    <p>اگر شما این اقدام را انجام نداده‌اید، رمز عبور خود را تغییر دهید و فوراً با پشتیبانی مشتری تماس بگیرید تا حسابتان مسدود شود.</p>
    <p>با احترام،</p>
    <p>تیم {{ $appName }}</p>
@endsection
