
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تست درگاه SEP</title>
</head>
<body>
<h1>تست درگاه پرداخت SEP</h1>

<p>
    مبلغ درخواست:
    <strong>۱۰٬۰۰۰ ریال</strong>
</p>

<p>
    این عملیات فقط برای دریافت توکن است و شما را به صفحه پرداخت منتقل نمی‌کند.
</p>

@if (session('sep_token_received'))
    <p style="color: green">
        توکن با موفقیت دریافت و در دیتابیس ذخیره شد.
    </p>

    <p>
        شماره درخواست:
        {{ session('sep_res_num') }}
    </p>
@endif

@error('payment')
<p style="color: red">{{ $message }}</p>
@enderror

<form
    method="POST"
    action="{{ route('payments.sep-test.token') }}"
    autocomplete="off"
>
    @csrf

    <button type="submit">
        ارسال درخواست توکن به SEP
    </button>
</form>
</body>
</html>
