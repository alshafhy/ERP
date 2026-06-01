<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عقد صفقة شراء رقم #{{ $deal->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: top;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            margin: 0;
        }
        .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table th {
            background-color: #f3f4f6;
            padding: 8px;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #e5e7eb;
            text-align: right;
        }
        .meta-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            border-right: 3px solid #4f46e5;
            padding-right: 8px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .financial-table th {
            background-color: #4f46e5;
            color: white;
            padding: 10px;
            font-weight: bold;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .financial-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .financial-table .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px dashed #666;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

    <!-- Header Letterhead -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 50%;">
                    <h1 class="title">معرض عماد الدين للسيارات</h1>
                    <p class="subtitle">الرياض، المملكة العربية السعودية | جوال: 0500000000</p>
                    <p class="subtitle">سجل تجاري: 1010000000 | الرقم الضريبي: 300000000000003</p>
                </td>
                <td style="width: 50%; text-align: left; font-size: 10px;">
                    <strong>رقم عقد الصفقة:</strong> #{{ $deal->id }}<br>
                    <strong>التاريخ:</strong> {{ $deal->created_at->format('Y-m-d') }}<br>
                    <strong>طريقة الشراء:</strong> <span style="text-transform: uppercase;">{{ $deal->deal_type }}</span><br>
                    <strong>حالة العقد:</strong> <span style="text-transform: uppercase;">{{ $deal->status }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Parties Info -->
    <div class="section-title">أولاً: أطراف التعاقد</div>
    <table class="meta-table">
        <tr>
            <th style="width: 20%;">الطرف الأول (البائع)</th>
            <td style="width: 30%;">معرض عماد الدين للسيارات</td>
            <th style="width: 20%;">الطرف الثاني (المشتري)</th>
            <td style="width: 30%;">{{ $deal->customer->name }}</td>
        </tr>
        <tr>
            <th>رقم السجل/الهوية</th>
            <td>1010000000</td>
            <th>رقم الهوية الوطنية</th>
            <td>{{ $deal->customer->national_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>رقم الجوال</th>
            <td>0500000000</td>
            <th>رقم جوال المشتري</th>
            <td>{{ $deal->customer->phone }}</td>
        </tr>
        <tr>
            <th>البريد الإلكتروني</th>
            <td>sales@emadeldin.com</td>
            <th>البريد الإلكتروني</th>
            <td>{{ $deal->customer->email ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Vehicle Details -->
    <div class="section-title">ثانياً: مواصفات المركبة المباعة</div>
    <table class="meta-table">
        <tr>
            <th style="width: 25%;">الشركة المصنعة والموديل</th>
            <td style="width: 25%;">{{ $deal->vehicle->make }} {{ $deal->vehicle->model }}</td>
            <th style="width: 25%;">سنة الصنع (الموديل)</th>
            <td style="width: 25%;">{{ $deal->vehicle->year }}</td>
        </tr>
        <tr>
            <th>رقم الهيكل (VIN)</th>
            <td style="font-family: monospace;">{{ $deal->vehicle->vin }}</td>
            <th>اللون الخارجي</th>
            <td>{{ $deal->vehicle->color }}</td>
        </tr>
        <tr>
            <th>نوع المحرك والوقود</th>
            <td>{{ $deal->vehicle->fuel_type }}</td>
            <th>ناقل الحركة</th>
            <td>{{ $deal->vehicle->transmission }}</td>
        </tr>
    </table>

    <!-- Trade-in Specs if any -->
    @if($deal->deal_type === 'trade_in')
        <div class="section-title">ثالثاً: تفاصيل المركبة المقايضة المستبدلة (Trade-in)</div>
        <table class="meta-table">
            <tr>
                <th style="width: 25%;">الشركة والموديل المستبدل</th>
                <td style="width: 25%;">{{ $deal->trade_in_make }} {{ $deal->trade_in_model }}</td>
                <th style="width: 25%;">سنة الصنع</th>
                <td style="width: 25%;">{{ $deal->trade_in_year }}</td>
            </tr>
            <tr>
                <th>رقم الهيكل (VIN)</th>
                <td style="font-family: monospace;">{{ $deal->trade_in_vin }}</td>
                <th>قيمة التثمين المقدرة</th>
                <td>{{ number_format($deal->trade_in_value, 2) }} ر.س</td>
            </tr>
        </table>
    @endif

    <!-- Financial Breakdown -->
    <div class="section-title">رابعاً: القيمة المالية والاتفاق البيعي</div>
    <table class="financial-table">
        <thead>
            <tr>
                <th>سعر اتفاق البيع</th>
                <th>الخصم الممنوح</th>
                @if($deal->deal_type === 'trade_in')
                    <th>تنزيل قيمة المقايضة (Trade-in)</th>
                @endif
                <th>الصافي النهائي المستحق للطلب</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ number_format($deal->agreed_price, 2) }} ر.س</td>
                <td style="color: red;">- {{ number_format($deal->discount, 2) }} ر.س</td>
                @if($deal->deal_type === 'trade_in')
                    <td style="color: #d97706;">- {{ number_format($deal->trade_in_value, 2) }} ر.س</td>
                @endif
                <td class="total-row">{{ number_format($deal->final_price, 2) }} ر.س</td>
            </tr>
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box" style="float: right;">
            <strong>توقيع الطرف الأول (البائع)</strong><br>
            <span style="font-size: 9px; color: #666;">عن معرض عماد الدين للسيارات</span>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box" style="float: left;">
            <strong>توقيع الطرف الثاني (المشترى)</strong><br>
            <span style="font-size: 9px; color: #666;">الاسم: {{ $deal->customer->name }}</span>
            <div class="signature-line"></div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        هذا العقد يعتبر اتفاقية بيع رسمية ومقيدة للطرفين بعد التوقيع والاعتماد المالي. معرض عماد الدين للسيارات - الرياض.
    </div>

</body>
</html>
