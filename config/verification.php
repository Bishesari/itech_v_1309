<?php
declare(strict_types=1);
return [

    'otp' => [
        'length' => 6,

        // مدت اعتبار OTP
        'expires_in' => 2,

        // فاصله حداقل بین دو ارسال مجدد
        'resend_cooldown' => 2,

        // محدودیت تعداد SMS برای هر موبایل
        'max_sends' => 3,
        'send_window' => 10,

        // محدودیت بر اساس fingerprint
        'fingerprint_max_sends' => 6,
        'fingerprint_send_window' => 10,

        // محدودیت بر اساس IP
        'ip_max_sends' => 30,
        'ip_send_window' => 10,

        // حداکثر تلاش برای وارد کردن OTP
        'max_attempts' => 5,
    ],

];
