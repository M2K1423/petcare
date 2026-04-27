<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

class VnpayService
{
    public function ensureConfigured(): void
    {
        if (! config('vnpay.enabled')) {
            throw new RuntimeException('VNPay is not enabled. Please set VNPAY_ENABLED=true.');
        }

        if (! config('vnpay.tmn_code') || ! config('vnpay.hash_secret')) {
            throw new RuntimeException('VNPay is missing VNPAY_TMN_CODE or VNPAY_HASH_SECRET.');
        }
    }

    public function createPaymentUrl(Payment $payment, Request $request): string
    {
        $this->ensureConfigured();

        if (! $payment->transaction_code) {
            $payment->forceFill([
                'transaction_code' => $this->buildTxnRef($payment),
            ])->save();
        }

        $returnUrl = config('vnpay.return_url') ?: route('payments.vnpay.return');

        $payload = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => config('vnpay.tmn_code'),
            'vnp_Amount' => (string) ((int) round((float) $payment->amount * 100)),
            'vnp_CreateDate' => Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $request->ip() ?: '127.0.0.1',
            'vnp_Locale' => config('vnpay.locale', 'vn'),
            'vnp_OrderInfo' => $this->sanitizeOrderInfo($this->buildOrderInfo($payment)),
            'vnp_OrderType' => config('vnpay.order_type', 'other'),
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef' => $payment->transaction_code,
            'vnp_ExpireDate' => Carbon::now('Asia/Ho_Chi_Minh')
                ->addMinutes((int) config('vnpay.expire_minutes', 15))
                ->format('YmdHis'),
        ];

        $query = $this->buildQuery($payload);
        $hash = hash_hmac('sha512', $query, (string) config('vnpay.hash_secret'));

        return rtrim((string) config('vnpay.payment_url'), '?') . '?' . $query . '&vnp_SecureHash=' . $hash;
    }

    /**
     * @param  array<string, string|null>  $params
     */
    public function verifySignature(array $params): bool
    {
        $secureHash = $params['vnp_SecureHash'] ?? null;

        if (! $secureHash) {
            return false;
        }

        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        $query = $this->buildQuery($params);
        $expected = hash_hmac('sha512', $query, (string) config('vnpay.hash_secret'));

        return hash_equals($expected, $secureHash);
    }

    /**
     * @param  array<string, string|null>  $params
     */
    public function isSuccessful(array $params): bool
    {
        return ($params['vnp_ResponseCode'] ?? '') === '00'
            && ($params['vnp_TransactionStatus'] ?? '') === '00';
    }

    public function buildTxnRef(Payment $payment): string
    {
        $prefix = $payment->medicine_order_id ? 'MED' : 'APT';

        return sprintf(
            'VNPAY-%s-%d-%s',
            $prefix,
            $payment->id,
            Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis')
        );
    }

    public function sanitizeOrderInfo(string $value): string
    {
        $ascii = Str::ascii($value);
        $clean = preg_replace('/[^A-Za-z0-9 .,:\-]/', ' ', $ascii) ?? $ascii;
        $singleLine = preg_replace('/\s+/', ' ', trim($clean)) ?? trim($clean);

        return Str::limit($singleLine, 255, '');
    }

    /**
     * @param  array<string, string|null>  $params
     */
    public function extractGatewayPayload(array $params): array
    {
        return Arr::where($params, static fn ($value, $key) => str_starts_with((string) $key, 'vnp_'));
    }

    public function buildOrderInfo(Payment $payment): string
    {
        if ($payment->medicine_order_id) {
            return "Thanh toan don thuoc {$payment->medicine_order_id}";
        }

        return "Thanh toan vien phi lich kham {$payment->appointment_id}";
    }

    /**
     * @param  array<string, string|null>  $params
     */
    private function buildQuery(array $params): string
    {
        $filtered = array_filter(
            $params,
            static fn ($value, $key) => str_starts_with((string) $key, 'vnp_') && $value !== null && $value !== '',
            ARRAY_FILTER_USE_BOTH,
        );

        ksort($filtered);

        $segments = [];

        foreach ($filtered as $key => $value) {
            $segments[] = urlencode((string) $key) . '=' . urlencode((string) $value);
        }

        return implode('&', $segments);
    }
}
