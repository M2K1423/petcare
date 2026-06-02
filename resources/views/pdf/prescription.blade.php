<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Thuốc</title>
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
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-top: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            color: #2A6496;
        }
        .medicine-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .medicine-list th, .medicine-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .medicine-list th {
            background-color: #f8f9fa;
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

    <div class="title">ĐƠN THUỐC THÚ Y</div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Chủ nuôi:</strong></td>
            <td width="35%">{{ $appointment->owner->name }}</td>
            <td width="15%"><strong>Điện thoại:</strong></td>
            <td width="35%">{{ $appointment->owner->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Thú cưng:</strong></td>
            <td>{{ $appointment->pet->name }} ({{ $appointment->pet->species->name ?? 'Không rõ' }})</td>
            <td><strong>Giới tính:</strong></td>
            <td>{{ $appointment->pet->gender == 'male' ? 'Đực' : ($appointment->pet->gender == 'female' ? 'Cái' : 'Không rõ') }}</td>
        </tr>
        <tr>
            <td><strong>Cân nặng:</strong></td>
            <td>{{ $record->weight_kg ?? '...' }} kg</td>
            <td><strong>Mã BA:</strong></td>
            <td>{{ $record->record_code ?? '...' }}</td>
        </tr>
    </table>

    <div class="section-title">CHẨN ĐOÁN</div>
    <p>{{ $record->final_diagnosis ?? $record->diagnosis ?? 'Không có thông tin' }}</p>

    <div class="section-title">CHỈ ĐỊNH ĐIỀU TRỊ / ĐƠN THUỐC</div>
    
    @if(!empty($record->prescriptions) && is_array($record->prescriptions))
        <table class="medicine-list">
            <thead>
                <tr>
                    <th width="5%">STT</th>
                    <th width="35%">Tên Thuốc</th>
                    <th width="20%">Liều lượng</th>
                    <th width="15%">Thời gian</th>
                    <th width="25%">Hướng dẫn</th>
                </tr>
            </thead>
            <tbody>
                @foreach($record->prescriptions as $idx => $med)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $med['medicine_name'] ?? 'Thuốc' }}</strong></td>
                    <td>{{ $med['dosage'] ?? 'Theo HD' }}</td>
                    <td>{{ $med['days'] ?? '1' }} ngày</td>
                    <td>{{ $med['instructions'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @elseif(!empty($record->treatment))
        <p>{!! nl2br(e($record->treatment)) !!}</p>
    @else
        <p>Không có chỉ định thuốc.</p>
    @endif

    @if(!empty($record->follow_up_plan) || !empty($record->notes))
    <div class="section-title">LỜI DẶN CỦA BÁC SĨ</div>
    @if(!empty($record->follow_up_plan))
        <p><strong>Lịch tái khám:</strong> {{ $record->follow_up_plan }}</p>
    @endif
    @if(!empty($record->notes))
        <p><strong>Ghi chú:</strong> {!! nl2br(e($record->notes)) !!}</p>
    @endif
    @endif

    <div class="footer">
        <div class="footer-left">
            <p><strong>Lưu ý:</strong> Khách hàng mang theo đơn này khi tái khám.</p>
        </div>
        <div class="footer-right">
            <p>Ngày xuất: {{ $date }}</p>
            <p><strong>Bác sĩ điều trị</strong></p>
            <div class="signature">
                {{ optional($appointment->doctor->user)->name ?? 'Bác sĩ' }}
            </div>
        </div>
    </div>
</body>
</html>
