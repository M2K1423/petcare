<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VnpayController extends Controller
{
    public function __construct(
        private readonly VnpayService $vnpay,
    ) {
    }

    public function ipn(Request $request): JsonResponse
    {
        $params = $this->normalizeParams($request);

        if (! $this->vnpay->verifySignature($params)) {
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid Signature',
            ]);
        }

        $payment = Payment::query()
            ->where('transaction_code', $params['vnp_TxnRef'] ?? '')
            ->first();

        if (! $payment) {
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Order not found',
            ]);
        }

        $expectedAmount = (int) round((float) $payment->amount * 100);
        $receivedAmount = (int) ($params['vnp_Amount'] ?? 0);

        if ($expectedAmount !== $receivedAmount) {
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Invalid amount',
            ]);
        }

        if ($payment->status === 'paid') {
            return response()->json([
                'RspCode' => '02',
                'Message' => 'Order already confirmed',
            ]);
        }

        $this->applyGatewayResult($payment, $params);

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }

    public function handleReturn(Request $request): RedirectResponse
    {
        $params = $this->normalizeParams($request);

        if (! $this->vnpay->verifySignature($params)) {
            return redirect()->route('receptionist.billing', [
                'paymentStatus' => 'invalid-signature',
                'paymentMessage' => 'Chu ky VNPay khong hop le.',
            ]);
        }

        $payment = Payment::query()
            ->where('transaction_code', $params['vnp_TxnRef'] ?? '')
            ->first();

        if (! $payment) {
            return redirect()->route('receptionist.billing', [
                'paymentStatus' => 'not-found',
                'paymentMessage' => 'Khong tim thay giao dich thanh toan.',
            ]);
        }

        if ($payment->status !== 'paid') {
            $this->applyGatewayResult($payment, $params);
        }

        $successful = $this->vnpay->isSuccessful($params);

        return redirect()->route('receptionist.billing', [
            'paymentStatus' => $successful ? 'success' : 'failed',
            'paymentMessage' => $successful
                ? 'Thanh toan VNPay thanh cong.'
                : 'Thanh toan VNPay khong thanh cong. Ma loi: ' . ($params['vnp_ResponseCode'] ?? 'N/A'),
        ]);
    }

    /**
     * @param  array<string, string|null>  $params
     */
    private function applyGatewayResult(Payment $payment, array $params): void
    {
        DB::transaction(function () use ($payment, $params) {
            $successful = $this->vnpay->isSuccessful($params);
            $paidAt = $this->parsePayDate($params['vnp_PayDate'] ?? null);

            $payment->forceFill([
                'gateway' => 'vnpay',
                'payment_method' => 'vnpay',
                'status' => $successful ? 'paid' : 'failed',
                'paid_at' => $successful ? ($paidAt ?? Carbon::now()) : null,
                'gateway_transaction_no' => $params['vnp_TransactionNo'] ?? null,
                'gateway_response_code' => $params['vnp_ResponseCode'] ?? null,
                'gateway_payload' => $this->vnpay->extractGatewayPayload($params),
                'notes' => $successful
                    ? 'Payment completed by VNPay.'
                    : 'VNPay payment failed with response code ' . ($params['vnp_ResponseCode'] ?? 'N/A') . '.',
            ])->save();

            if ($payment->medicineOrder) {
                $payment->medicineOrder->forceFill([
                    'status' => $successful ? 'paid' : 'confirmed',
                    'paid_at' => $successful ? ($paidAt ?? Carbon::now()) : null,
                ])->save();
            }
        });
    }

    /**
     * @return array<string, string|null>
     */
    private function normalizeParams(Request $request): array
    {
        return collect($request->query())
            ->mapWithKeys(static fn ($value, $key) => [$key => is_scalar($value) ? (string) $value : null])
            ->all();
    }

    private function parsePayDate(?string $payDate): ?Carbon
    {
        if (! $payDate) {
            return null;
        }

        try {
            return Carbon::createFromFormat('YmdHis', $payDate, 'Asia/Ho_Chi_Minh');
        } catch (\Throwable) {
            return null;
        }
    }
}
