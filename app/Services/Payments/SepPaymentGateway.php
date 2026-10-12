<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SepPaymentGateway
{
    /**
     * Request a payment token from SEP.
     *
     * This method does not initiate or confirm a payment.
     */
    public function requestToken(
        int $amount,
        string $resNum,
        string $redirectUrl,
        ?string $cellNumber = null,
    ): string {
        $terminalId = config('services.sep.terminal_id');

        if (blank($terminalId)) {
            throw new RuntimeException(
                'SEP terminal ID is not configured.'
            );
        }

        if ($amount <= 0) {
            throw new RuntimeException(
                'Payment amount must be greater than zero.'
            );
        }

        if (blank($resNum)) {
            throw new RuntimeException(
                'Payment reservation number is required.'
            );
        }

        if (
            parse_url($redirectUrl, PHP_URL_SCHEME) !== 'https'
            || ! parse_url($redirectUrl, PHP_URL_HOST)
        ) {
            throw new RuntimeException(
                'SEP callback URL must use HTTPS.'
            );
        }

        $payload = [
            'Action' => 'Token',
            'TerminalId' => $terminalId,
            'Amount' => $amount,
            'ResNum' => $resNum,
            'RedirectUrl' => $redirectUrl,
        ];

        if ($cellNumber !== null && $cellNumber !== '') {
            $payload['CellNumber'] = $cellNumber;
        }

        $response = Http::asJson()
            ->acceptJson()
            ->connectTimeout(
                config('services.sep.connect_timeout', 5)
            )
            ->timeout(
                config('services.sep.timeout', 15)
            )
            ->post(
                config('services.sep.token_url'),
                $payload
            );

        if (! $response->successful()) {
            throw new RuntimeException(
                'SEP token request failed with HTTP status '
                .$response->status().'.'
            );
        }

        $result = $response->json();

        if (
            ! is_array($result)
            || (int) ($result['status'] ?? 0) !== 1
            || ! is_string($result['token'] ?? null)
            || trim($result['token']) === ''
        ) {
            throw new RuntimeException(
                'SEP did not return a valid payment token.'
            );
        }

        return $result['token'];
    }
}
