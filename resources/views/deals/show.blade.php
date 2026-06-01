@extends('layouts.admin')

@section('title', 'تفاصيل عقد الصفقة رقم #' . $deal->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('deals.index') }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl transition duration-150">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">تفاصيل عقد الصفقة رقم #{{ $deal->id }}</h1>
                <p class="text-xs text-gray-500 mt-1">تاريخ الإنشاء: <span class="font-mono text-gray-700">{{ $deal->created_at->format('Y-m-d H:i') }}</span> | بواسطة: {{ $deal->salesperson->name ?? 'نظام المبيعات' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- PDF Download -->
            <a href="{{ route('deals.pdf', $deal) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition duration-150 gap-1.5">
                <i class="fas fa-file-pdf"></i>
                <span>تحميل العقد PDF</span>
            </a>
        </div>
    </div>

    <!-- Notification Toast Logger -->
    @if(session()->has('notification_sent'))
        <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 p-6 rounded-2xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold">تم إرسال إشعارات العميل بنجاح!</h4>
                    <p class="text-[10px] text-gray-500">تمت محاكاة القنوات البريدية والرسائل النصية للعميل {{ $deal->customer->name }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">
                <div class="bg-white p-4 rounded-xl border border-gray-150">
                    <span class="text-[10px] text-indigo-500 block mb-1.5"><i class="fas fa-comment-alt"></i> رسالة الجوال (SMS)</span>
                    <p class="text-gray-700 font-normal leading-relaxed">{{ session('notification_sent')['sms'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-gray-150">
                    <span class="text-[10px] text-indigo-500 block mb-1.5"><i class="fas fa-envelope"></i> البريد الإلكتروني</span>
                    <p class="text-gray-700 font-normal leading-relaxed">{{ session('notification_sent')['email'] }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Timeline of Deal status -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xs font-bold text-gray-700 mb-6 pb-2 border-b border-gray-50"><i class="fas fa-route ml-2 text-indigo-500"></i>مراحل الصفقة وحالة العقد</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @foreach([
                'draft' => ['name' => 'مسودة', 'icon' => 'fa-edit', 'color' => 'bg-gray-100 text-gray-500 border-gray-200'],
                'pending_approval' => ['name' => 'انتظار الاعتماد', 'icon' => 'fa-hourglass-half', 'color' => 'bg-amber-50 text-amber-600 border-amber-200'],
                'approved' => ['name' => 'تم الاعتماد', 'icon' => 'fa-check-circle', 'color' => 'bg-blue-50 text-blue-600 border-blue-200'],
                'contract_signed' => ['name' => 'العقد موقع', 'icon' => 'fa-file-signature', 'color' => 'bg-indigo-50 text-indigo-600 border-indigo-200'],
                'delivered' => ['name' => 'تم التسليم 🔑', 'icon' => 'fa-key', 'color' => 'bg-emerald-50 text-emerald-600 border-emerald-200'],
                'closed' => ['name' => 'مغلقة', 'icon' => 'fa-lock', 'color' => 'bg-green-150 text-green-800 border-green-300']
            ] as $statusKey => $statusDetails)
                @php
                    $isCurrent = $deal->status === $statusKey;
                @endphp
                <div class="flex flex-col items-center p-4 rounded-xl border text-center transition duration-150 {{ $isCurrent ? $statusDetails['color'] . ' ring-2 ring-indigo-500/20 shadow-sm scale-105' : 'bg-gray-50/50 text-gray-400 border-gray-100' }}">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center mb-2 {{ $isCurrent ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fas {{ $statusDetails['icon'] }} text-xs"></i>
                    </span>
                    <span class="text-xs font-bold block">{{ $statusDetails['name'] }}</span>
                    @if($isCurrent)
                        <span class="text-[8px] font-black uppercase tracking-wider block mt-1">الحالة الحالية</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Right Column: Profiles & Financial recap -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Financial Invoice block -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-6 pb-2 border-b border-gray-50"><i class="fas fa-file-invoice-dollar ml-2 text-indigo-500"></i>الحسابات وتفاصيل الفاتورة المالية</h3>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">سعر الاتفاق</span>
                        <span class="text-base font-extrabold text-gray-800 font-mono">{{ number_format($deal->agreed_price, 2) }} ر.س</span>
                    </div>

                    <div class="p-4 bg-red-50/50 rounded-xl border border-red-100 text-red-700">
                        <span class="text-[10px] text-red-500 block mb-1">الخصم المقدم</span>
                        <span class="text-base font-extrabold font-mono">- {{ number_format($deal->discount, 2) }} ر.س</span>
                    </div>

                    <div class="p-4 bg-amber-50/50 rounded-xl border border-amber-100 text-amber-700">
                        <span class="text-[10px] text-amber-600 block mb-1">تثمين المقايضة (Trade-in)</span>
                        <span class="text-base font-extrabold font-mono">- {{ number_format($deal->trade_in_value, 2) }} ر.س</span>
                    </div>

                    <div class="p-4 bg-indigo-900 rounded-xl text-white">
                        <span class="text-[10px] text-indigo-200 block mb-1">الصافي النهائي المستحق</span>
                        <span class="text-base font-extrabold font-mono">{{ number_format($deal->final_price, 2) }} ر.س</span>
                    </div>
                </div>
            </div>

            <!-- Customer & Vehicle specs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Details -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="text-xs font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-user-circle ml-2 text-indigo-500"></i>بيانات المشتري</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-400">الاسم الكامل:</span>
                            <a href="{{ route('customers.show', $deal->customer) }}" class="font-extrabold text-indigo-600 hover:underline">{{ $deal->customer->name }}</a>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">رقم الجوال:</span>
                            <span class="font-bold text-gray-800 font-mono">{{ $deal->customer->phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">الهوية الوطنية / السجل:</span>
                            <span class="font-bold text-gray-800 font-mono">{{ $deal->customer->national_id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">العنوان:</span>
                            <span class="font-bold text-gray-700">{{ $deal->customer->address ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Details -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="text-xs font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-car ml-2 text-indigo-500"></i>المركبة المباعة</h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-400">المركبة:</span>
                            <a href="{{ route('vehicles.show', $deal->vehicle) }}" class="font-extrabold text-indigo-600 hover:underline">
                                {{ $deal->vehicle->make }} {{ $deal->vehicle->model }} ({{ $deal->vehicle->year }})
                            </a>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">رقم الهيكل (VIN):</span>
                            <span class="font-bold text-gray-800 font-mono">{{ $deal->vehicle->vin }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">اللون الفني:</span>
                            <span class="font-bold text-gray-700">{{ $deal->vehicle->color }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">المحرك والناقل:</span>
                            <span class="font-bold text-gray-700">{{ $deal->vehicle->fuel_type }} | {{ $deal->vehicle->transmission }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trade-in Vehicle specifications if present -->
            @if($deal->deal_type === 'trade_in')
                <div class="bg-amber-50/30 p-6 rounded-2xl border border-amber-100 space-y-4">
                    <h3 class="text-xs font-bold text-amber-900 pb-2 border-b border-amber-100"><i class="fas fa-exchange-alt ml-2"></i>مواصفات المقايضة (Trade-in Vehicle)</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-semibold">
                        <div>
                            <span class="text-[10px] text-amber-600 block">الشركة والموديل</span>
                            <span class="text-gray-800 font-bold block mt-1">{{ $deal->trade_in_make }} {{ $deal->trade_in_model }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-600 block">سنة الصنع</span>
                            <span class="text-gray-800 font-bold block mt-1">{{ $deal->trade_in_year }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-600 block">رقم الهيكل (VIN)</span>
                            <span class="text-gray-800 font-mono font-bold block mt-1">{{ $deal->trade_in_vin }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-600 block">تثمين مقاصة الاستبدال</span>
                            <span class="text-amber-800 font-mono font-bold block mt-1">{{ number_format($deal->trade_in_value, 2) }} ر.س</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Finance, Receipts & Payments Ledger -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                <div class="flex justify-between items-center pb-3 border-b border-gray-150">
                    <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-wallet ml-2 text-indigo-500"></i>دفتر المدفوعات وسندات القبض</h3>
                    <a href="{{ route('deals.invoice', $deal) }}" class="px-3 py-1.5 bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 font-bold text-xs rounded-xl border border-gray-200 transition">
                        <i class="fas fa-file-invoice-dollar ml-1"></i> تحميل كشف الحساب المالي PDF
                    </a>
                </div>

                <!-- Record a general payment form -->
                @if($deal->status !== 'closed')
                    <form action="{{ route('deals.payments.store', $deal) }}" method="POST" class="p-4 bg-indigo-50/30 border border-indigo-100 rounded-xl space-y-3">
                        @csrf
                        <span class="text-xs font-bold text-indigo-900 block"><i class="fas fa-plus-circle ml-1.5"></i>تسجيل سند قبض مالي جديد (تحصيل نقدي/بنكي)</span>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1">المبلغ المحصل *</label>
                                <input type="number" step="0.01" name="amount" required min="1" class="w-full py-1.5 px-3 rounded-lg border border-gray-200 text-xs font-mono font-bold text-right" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1">طريقة القبض *</label>
                                <select name="method" required class="w-full py-1.5 px-2.5 rounded-lg border border-gray-200 text-xs text-right bg-white">
                                    <option value="cash">نقدي (Cash)</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="cheque">شيك مصرفي</option>
                                    <option value="card">بطاقة مدى</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 mb-1">الرقم المرجعي</label>
                                <input type="text" name="reference" class="w-full py-1.5 px-3 rounded-lg border border-gray-200 text-xs text-right" placeholder="مثال: رقم الحوالة">
                            </div>
                            <input type="hidden" name="paid_at" value="{{ date('Y-m-d') }}">
                            <div class="flex items-end">
                                <button type="submit" class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                    إيداع السند
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                <!-- Payment traces list -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-gray-700 block">سندات القبض المودعة:</span>
                    @if($deal->payments->isEmpty())
                        <div class="text-center py-6 text-gray-400 text-xs bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                            لم تسجل أية إيداعات أو دفعات حتى الآن لهذا العقد.
                        </div>
                    @else
                        <div class="overflow-x-auto border border-gray-100 rounded-xl">
                            <table class="w-full text-right border-collapse text-xs">
                                <thead class="bg-gray-50 text-gray-500">
                                    <tr>
                                        <th class="px-4 py-2">رقم السند</th>
                                        <th class="px-4 py-2">المبلغ</th>
                                        <th class="px-4 py-2">الطريقة</th>
                                        <th class="px-4 py-2">المرجع</th>
                                        <th class="px-4 py-2">تاريخ الدفع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deal->payments as $pay)
                                        <tr class="border-b border-gray-50">
                                            <td class="px-4 py-2.5 font-bold font-mono text-gray-700">#{{ $pay->id }}</td>
                                            <td class="px-4 py-2.5 font-extrabold font-mono text-emerald-600">{{ number_format($pay->amount, 2) }} ر.س</td>
                                            <td class="px-4 py-2.5 capitalize text-gray-600">{{ $pay->method }}</td>
                                            <td class="px-4 py-2.5 font-mono text-gray-500">{{ $pay->reference ?? '-' }}</td>
                                            <td class="px-4 py-2.5 font-mono text-gray-400">{{ $pay->paid_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Installment Plan Section -->
                @if($deal->deal_type === 'installment')
                    <div class="pt-6 border-t border-gray-150 space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-bold text-gray-700"><i class="fas fa-calendar-alt ml-1.5 text-amber-500"></i>جدول سداد الأقساط التمويلية</h4>
                            <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-0.5 rounded-lg">شراء بالتقسيط المعرض</span>
                        </div>

                        <!-- Installment plan Builder form if none generated -->
                        @if($deal->installments->isEmpty())
                            @if($deal->status !== 'closed')
                                <form action="{{ route('deals.installments.build', $deal) }}" method="POST" class="p-5 bg-amber-50/40 border border-amber-100 rounded-xl space-y-4">
                                    @csrf
                                    <span class="text-xs font-bold text-amber-900 block"><i class="fas fa-tools ml-1.5"></i>منشئ خطة جدول الأقساط (Installment Builder)</span>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[10px] text-amber-800 mb-1">الدفعة الأولى المقدمة (Downpayment) *</label>
                                            <input type="number" step="0.01" name="down_payment" value="0.00" required min="0" class="w-full py-1.5 px-3 rounded-lg border border-amber-200 text-xs font-mono font-bold text-right">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-amber-800 mb-1">عدد شهور/أقساط السداد *</label>
                                            <input type="number" name="number_of_installments" value="12" required min="1" max="120" class="w-full py-1.5 px-3 rounded-lg border border-amber-200 text-xs font-mono font-bold text-right">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] text-amber-800 mb-1">تاريخ استحقاق أول قسط *</label>
                                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full py-1.5 px-3 rounded-lg border border-amber-200 text-xs text-right">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                        تقسيم وبناء خطة جدول الأقساط تلقائياً
                                    </button>
                                </form>
                            @endif
                        @else
                            <!-- List generated installments -->
                            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                                <table class="w-full text-right border-collapse text-xs">
                                    <thead class="bg-gray-50 text-gray-500">
                                        <tr>
                                            <th class="px-4 py-2">الدفعة/القسط</th>
                                            <th class="px-4 py-2">القيمة</th>
                                            <th class="px-4 py-2">تاريخ الاستحقاق</th>
                                            <th class="px-4 py-2">الحالة</th>
                                            <th class="px-4 py-2 text-left">التحصيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deal->installments as $inst)
                                            <tr class="border-b border-gray-50">
                                                <td class="px-4 py-2.5 font-bold text-gray-700">
                                                    {{ $inst->installment_number == 0 ? 'دفعة مقدمة' : 'قسط شهري #' . $inst->installment_number }}
                                                </td>
                                                <td class="px-4 py-2.5 font-bold font-mono text-gray-800">{{ number_format($inst->amount, 2) }} ر.س</td>
                                                <td class="px-4 py-2.5 font-mono text-gray-500">{{ $inst->due_at->format('Y-m-d') }}</td>
                                                <td class="px-4 py-2.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold capitalize
                                                        @if($inst->status === 'paid') bg-emerald-50 text-emerald-700
                                                        @elseif($inst->status === 'overdue') bg-red-50 text-red-700 border border-red-100 animate-pulse
                                                        @else bg-amber-50 text-amber-700 border border-amber-100 @endif">
                                                        {{ $inst->status === 'paid' ? 'تم تحصيله' : ($inst->status === 'overdue' ? 'متأخر' : 'مستحق') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2.5 text-left">
                                                    @if($inst->status !== 'paid')
                                                        @if($deal->status !== 'closed')
                                                            <form action="{{ route('installments.pay', $inst) }}" method="POST" class="inline-flex gap-1">
                                                                @csrf
                                                                <input type="hidden" name="method" value="cash">
                                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded-lg transition" onclick="return confirm('تأكيد استلام قيمة القسط نقداً وحفظ السند؟')">
                                                                    تسجيل تحصيل 💵
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-400 font-mono text-[9px]">تم السداد في {{ $inst->paid_at->format('Y-m-d') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Left Column: Approvals & Operations -->
        <div class="space-y-6">
            <!-- Operations / Actions Panel -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-cogs ml-2 text-indigo-500"></i>إجراءات المبيعات وسير العمل</h3>

                <!-- Workflow status triggers -->
                <div class="space-y-3">
                    @if($deal->status === 'draft')
                        <form action="{{ route('deals.update-status', $deal) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                <i class="fas fa-paper-plane ml-1"></i> إرسال لطلب الاعتماد المالي
                            </button>
                        </form>
                    @endif

                    @if($deal->status === 'pending_approval')
                        <!-- Approval conditions -->
                        @if($deal->requiresApproval())
                            <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 text-[10px] text-amber-700 leading-relaxed mb-3">
                                <i class="fas fa-exclamation-triangle ml-1"></i> 
                                تنبيه: قيمة الصفقة الإجمالية تتجاوز 200,000 ريال. تتطلب موافقة معتمدة من <strong>مدير الفرع (Branch Manager)</strong> أو المشرف العام.
                            </div>
                        @endif

                        @can('approve', $deal)
                            <form action="{{ route('deals.update-status', $deal) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                    <i class="fas fa-check-double ml-1"></i> اعتماد وموافقة الصفقة رسمياً
                                </button>
                            </form>
                        @else
                            <div class="text-center py-2 text-[10px] text-gray-400 font-bold bg-gray-50 rounded-lg">
                                <i class="fas fa-lock ml-1"></i> لا تملك صلاحية اعتماد هذه الاتفاقية
                            </div>
                        @endcan
                    @endif

                    @if($deal->status === 'approved')
                        <form action="{{ route('deals.update-status', $deal) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="sign_contract">
                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                <i class="fas fa-file-signature ml-1"></i> توثيق وتوقيع العقد النهائي
                            </button>
                        </form>
                    @endif

                    @if($deal->status === 'contract_signed')
                        <form action="{{ route('deals.update-status', $deal) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="deliver">
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                <i class="fas fa-key ml-1"></i> تسليم السيارة للمشتري 🔑
                            </button>
                        </form>
                    @endif

                    @if($deal->status === 'delivered')
                        <form action="{{ route('deals.update-status', $deal) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="close">
                            <button type="submit" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                <i class="fas fa-archive ml-1"></i> إغلاق وحفظ ملف العقد
                            </button>
                        </form>
                    @endif

                    @if($deal->status !== 'closed' && $deal->status !== 'draft')
                        <form action="{{ route('deals.update-status', $deal) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الصفقة؟ سيؤدي ذلك لفك حجز السيارة بالمخزن.');" class="pt-2">
                            @csrf
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-xs rounded-xl transition border border-red-150">
                                إلغاء الصفقة وتعديلها كمسودة
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
