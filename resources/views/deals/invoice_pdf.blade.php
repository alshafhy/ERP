<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة وكشف حساب الصفقة رقم #{{ $deal->id }}</title>
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
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .ledger-table th {
            background-color: #f3f4f6;
            padding: 8px;
            font-weight: bold;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            text-align: center;
        }
        .ledger-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
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
                    <p class="subtitle">الرياض، المملكة العربية السعودية | كشف حساب الصفقة المالي</p>
                    <p class="subtitle">سجل تجاري: 1010000000 | الرقم الضريبي: 300000000000003</p>
                </td>
                <td style="width: 50%; text-align: left; font-size: 10px;">
                    <strong>رقم الصفقة:</strong> #{{ $deal->id }}<br>
                    <strong>التاريخ:</strong> {{ date('Y-m-d') }}<br>
                    <strong>حالة العقد:</strong> {{ strtoupper($deal->status) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Client / Car summaries -->
    <div class="section-title">بيانات العميل والسيارة</div>
    <table class="meta-table">
        <tr>
            <th style="width: 20%;">العميل</th>
            <td style="width: 30%;">{{ $deal->customer->name }}</td>
            <th style="width: 20%;">السيارة المباعة</th>
            <td style="width: 30%;">{{ $deal->vehicle->make }} {{ $deal->vehicle->model }} ({{ $deal->vehicle->year }})</td>
        </tr>
        <tr>
            <th>الجوال</th>
            <td>{{ $deal->customer->phone }}</td>
            <th>رقم الهيكل (VIN)</th>
            <td style="font-family: monospace;">{{ $deal->vehicle->vin }}</td>
        </tr>
    </table>

    <!-- Financial Statement -->
    <div class="section-title">الملخص المالي والاتفاق</div>
    <table class="meta-table">
        <tr>
            <th style="width: 25%;">سعر الاتفاق</th>
            <td style="width: 25%;">{{ number_format($deal->agreed_price, 2) }} ر.س</td>
            <th style="width: 25%;">الخصم الممنوح</th>
            <td style="width: 25%; color: red;">- {{ number_format($deal->discount, 2) }} ر.س</td>
        </tr>
        @if($deal->deal_type === 'trade_in')
            <tr>
                <th>تنزيل المقايضة</th>
                <td>- {{ number_format($deal->trade_in_value, 2) }} ر.س</td>
                <th>السيارة المستبدلة</th>
                <td>{{ $deal->trade_in_make }} {{ $deal->trade_in_model }}</td>
            </tr>
        @endif
        <tr>
            <th style="background-color: #4f46e5; color: white;">الصافي الإجمالي المطلوب</th>
            <td colspan="3" style="font-weight: bold; background-color: #f3f4f6;">{{ number_format($deal->final_price, 2) }} ر.س</td>
        </tr>
    </table>

    <!-- Payments logged -->
    <div class="section-title">سجل المدفوعات وسندات القبض المودعة</div>
    <table class="ledger-table">
        <thead>
            <tr>
                <th>الرقم المرجعي</th>
                <th>المبلغ المودع</th>
                <th>طريقة السداد</th>
                <th>تاريخ السداد</th>
                <th>تفاصيل الإيداع</th>
            </tr>
        </thead>
        <tbody>
            @if($deal->payments->isEmpty())
                <tr>
                    <td colspan="5" style="color: #999;">لم تسجل أية سندات قبض مالية حتى الآن.</td>
                </tr>
            @else
                @foreach($deal->payments as $pay)
                    <tr>
                        <td style="font-family: monospace;">{{ $pay->reference ?? 'N/A' }}</td>
                        <td style="font-weight: bold;">{{ number_format($pay->amount, 2) }} ر.س</td>
                        <td>{{ strtoupper($pay->method) }}</td>
                        <td>{{ $pay->paid_at ? $pay->paid_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>سند قبض رقم #{{ $pay->id }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Installments scheduled if any -->
    @if($deal->deal_type === 'installment' && !$deal->installments->isEmpty())
        <div class="section-title">خطة السداد وجدول الأقساط المعتمد</div>
        <table class="ledger-table">
            <thead>
                <tr>
                    <th>القسط رقم</th>
                    <th>المبلغ المستحق</th>
                    <th>تاريخ الاستحقاق</th>
                    <th>حالة السداد</th>
                    <th>تاريخ الدفع الفعلي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deal->installments as $inst)
                    <tr>
                        <td style="font-weight: bold;">
                            {{ $inst->installment_number == 0 ? 'دفعة مقدمة' : 'قسط #' . $inst->installment_number }}
                        </td>
                        <td style="font-weight: bold;">{{ number_format($inst->amount, 2) }} ر.س</td>
                        <td>{{ $inst->due_at->format('Y-m-d') }}</td>
                        <td style="font-weight: bold; color: {{ $inst->status === 'paid' ? 'green' : ($inst->status === 'overdue' ? 'red' : 'orange') }};">
                            {{ $inst->status === 'paid' ? 'تم السداد' : ($inst->status === 'overdue' ? 'متأخر' : 'مستحق قريباً') }}
                        </td>
                        <td>{{ $inst->paid_at ? $inst->paid_at->format('Y-m-d') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        هذه الفاتورة وكشف الحساب تعتبر مستنداً مالياً رسمياً صادراً عن معرض عماد الدين للسيارات بالرياض.
    </div>

</body>
</html>
