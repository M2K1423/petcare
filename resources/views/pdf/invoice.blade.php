<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2A6496;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .clinic-name {
            font-size: 20px;
            font-weight: bold;
            color: #2A6496;
            text-transform: uppercase;
        }
        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .invoice-items th, .invoice-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .invoice-items th {
            background-color: #f8f9fa;
            text-align: center;
        }
        .invoice-items td.money {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .footer-left {
            float: left;
            width: 50%;
        }
        .footer-right {
            float: right;
            width: 50%;
            text-align: center;
        }
        .signature {
            margin-top: 60px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="clinic-name">{{ $clinic['name'] }}</div>
        <div>Địa chỉ: {{ $clinic['address'] }}</div>
        <div>SĐT: {{ $clinic['phone'] }} | Email: {{ $clinic['email'] }}</div>
    </div>

    <div class="title">HÓA ĐƠN THANH TOÁN</div>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Mã giao dịch:</strong></td>
            <td width="30%">{{ $payment->transaction_code ?? 'TXN-'.$payment->id }}</td>
            <td width="20%"><strong>Khách hàng:</strong></td>
            <td width="30%">{{ $payment->owner->name ?? ($payment->appointment->owner->name ?? '') }}</td>
        </tr>
        <tr>
            <td><strong>Ngày thanh toán:</strong></td>
            <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : $date }}</td>
            <td><strong>Điện thoại:</strong></td>
            <td>{{ $payment->owner->phone ?? ($payment->appointment->owner->phone ?? 'N/A') }}</td>
        </tr>
        <tr>
            <td><strong>Phương thức:</strong></td>
            <td>{{ strtoupper($payment->payment_method ?? 'CASH') }}</td>
            <td><strong>Thú cưng:</strong></td>
            <td>
                @if($payment->appointment)
                    {{ $payment->appointment->pet->name ?? '' }} ({{ $payment->appointment->pet->species->name ?? '' }})
                @elseif($payment->medicineOrder)
                    {{ $payment->medicineOrder->pet->name ?? '' }}
                @endif
            </td>
        </tr>
    </table>

    <table class="invoice-items">
        <thead>
            <tr>
                <th width="5%">STT</th>
                <th width="50%">Nội dung / Dịch vụ</th>
                <th width="15%">Số lượng</th>
                <th width="30%">Thành tiền (VND)</th>
            </tr>
        </thead>
        <tbody>
            @php $stt = 1; $total = 0; @endphp

            {{-- Dịch vụ khám/chữa bệnh --}}
            @if($payment->appointment && $payment->appointment->service)
                @php 
                    $price = $payment->appointment->service->price ?? 0;
                    $total += $price;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $stt++ }}</td>
                    <td>Phí dịch vụ: {{ $payment->appointment->service->name }}</td>
                    <td style="text-align: center;">1</td>
                    <td class="money">{{ number_format($price, 0, ',', '.') }}</td>
                </tr>
            @endif

            {{-- Đơn thuốc --}}
            @if($payment->medicineOrder && $payment->medicineOrder->items)
                @foreach($payment->medicineOrder->items as $item)
                    @php 
                        $line_total = $item->line_total ?? ($item->quantity * ($item->medicine->price ?? 0));
                        $total += $line_total;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $stt++ }}</td>
                        <td>{{ $item->medicine->name ?? 'Thuốc' }} {{ $item->medicine->unit ? '('.$item->medicine->unit.')' : '' }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td class="money">{{ number_format($line_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif

            {{-- Nếu không có mục chi tiết nào nhưng có tổng tiền Payment --}}
            @if($stt === 1 && $payment->amount > 0)
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Thanh toán theo mã: {{ $payment->transaction_code }}</td>
                    <td style="text-align: center;">1</td>
                    <td class="money">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                @php $total = $payment->amount; @endphp
            @endif

            {{-- Dòng tổng cộng --}}
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TỔNG CỘNG:</td>
                <td class="money">{{ number_format($payment->amount ?? $total, 0, ',', '.') }} VNĐ</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-left">
            <p><strong>Ghi chú:</strong> {{ $payment->notes ?? '' }}</p>
            <p>Cảm ơn Quý khách đã sử dụng dịch vụ của {{ $clinic['name'] }}!</p>
        </div>
        <div class="footer-right">
            <p><strong>Nhân viên lập phiếu</strong></p>
            <div class="signature">
                Thu Ngân
            </div>
        </div>
    </div>
</body>
</html>
