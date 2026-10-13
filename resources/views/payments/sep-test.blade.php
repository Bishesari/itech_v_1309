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
    مبلغ پرداخت:
    <strong>۱۰۰٬۰۰۰ ریال (۱۰٬۰۰۰ تومان)</strong>
</p>

<p>
    با کلیک روی دکمه زیر به صفحه پرداخت عملیاتی SEP منتقل می‌شوید.
    در صورت تأیید پرداخت توسط بانک، نتیجه از سمت سرور بررسی خواهد شد.
</p>

@error('payment')
<p style="color: red">{{ $message }}</p>
@enderror


<form
    method="POST"
    action="{{ route('products.buy', ['product' => $product]) }}"
    autocomplete="off"
>
    @csrf

    <button type="submit">
        پرداخت ۱۰۰٬۰۰۰ ریال
    </button>
</form>

</body>
</html>
