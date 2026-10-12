<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتیجه پرداخت SEP</title>
</head>
<body>
<h1>نتیجه پرداخت</h1>

@if ($paymentStatus === 'paid')
    <p>پرداخت با موفقیت تأیید شد.</p>
@elseif ($paymentStatus === 'pending')
    <p>
        تأیید پرداخت هنوز کامل نشده است.
        لطفاً از پرداخت مجدد خودداری کنید و وضعیت تراکنش را بررسی کنید.
    </p>
@else
    <p>
        پرداخت تأیید نشد. اگر مبلغی از حساب شما کسر شده است،
        پیش از تلاش مجدد وضعیت تراکنش را بررسی کنید.
    </p>
@endif

<a href="{{ route('payments.sep-test') }}">
    بازگشت به صفحه تست
</a>
</body>
</html>
