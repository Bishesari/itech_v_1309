<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتیجه تست SEP</title>
</head>
<body>
<h1>نتیجه بازگشت از درگاه</h1>

@if ($callbackReceived)
    <p>درخواست بازگشت دریافت شد.</p>
@endif

<p>وضعیت داخلی پرداخت: {{ $paymentStatus }}</p>
<p>این نتیجه به معنی تأیید پرداخت نیست.</p>
</body>
</html>
