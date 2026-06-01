@extends('layouts.admin')

@section('title', 'اللوحة المالية والحسابات')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">الحسابات والرقابة المالية</h1>
            <p class="text-sm text-gray-500 mt-1">تتبع التدفق المالي، الإيرادات المجمعة، الأقساط المستحقة وعمولات مناديب المبيعات</p>
        </div>
        
        <form action="{{ route('finance.scan') }}" method="POST">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-sm transition gap-1.5">
                <i class="fas fa-sync-alt animate-spin"></i>
                <span>فحص وتحديث الأقساط المتأخرة 🔄</span>
            </button>
        </form>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2">
            <span class="text-xs text-gray-400 block"><i class="fas fa-coins ml-1.5 text-indigo-500"></i>إجمالي المبيعات (Revenue)</span>
            <h4 class="text-xl font-black text-gray-800 font-mono">{{ number_format($totalRevenue, 2) }} <span class="text-xs font-normal">ر.س</span></h4>
            <p class="text-[9px] text-gray-500">من الصفقات المعتمدة والمسلمة</p>
        </div>

        <!-- Total Collected -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2">
            <span class="text-xs text-gray-400 block"><i class="fas fa-check-circle ml-1.5 text-emerald-500"></i>المبالغ المجمعة المحصلة</span>
            <h4 class="text-xl font-black text-emerald-600 font-mono">{{ number_format($totalCollected, 2) }} <span class="text-xs font-normal">ر.س</span></h4>
            <p class="text-[9px] text-emerald-500">سندات القبض المودعة بالحساب</p>
        </div>

        <!-- Total Outstanding -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2">
            <span class="text-xs text-gray-400 block"><i class="fas fa-hourglass-half ml-1.5 text-amber-500"></i>المبالغ المتبقية المستحقة</span>
            <h4 class="text-xl font-black text-amber-600 font-mono">{{ number_format($totalOutstanding, 2) }} <span class="text-xs font-normal">ر.س</span></h4>
            <p class="text-[9px] text-amber-500">باقي مستحقات عقود التمويل والأقساط</p>
        </div>

        <!-- Overdue Installments Count -->
        <div class="bg-red-50/50 p-6 rounded-2xl border border-red-100 space-y-2 text-red-900">
            <div class="flex justify-between items-center">
                <span class="text-xs text-red-700 font-bold"><i class="fas fa-exclamation-triangle ml-1.5"></i>الأقساط المتأخرة</span>
                <span class="animate-ping inline-flex h-2 w-2 rounded-full bg-red-400"></span>
            </div>
            <h4 class="text-2xl font-black font-mono">{{ $overdueCount }}</h4>
            <p class="text-[9px] text-red-600">تجاوزت تاريخ الاستحقاق دون سداد</p>
        </div>
    </div>

    <!-- Main Content Tabs/Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Installments Ledger (2/3 width) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-right">
            <div class="px-6 py-4 border-b border-gray-150 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xs font-bold text-gray-700"><i class="fas fa-list-ol ml-2 text-indigo-500"></i>جدول وجدول سداد الأقساط للمشترين</h3>
                
                <form action="{{ route('finance.index') }}" method="GET" class="flex gap-2">
                    <select name="status" onchange="this.form.submit();" class="py-1 px-2.5 rounded-lg border border-gray-200 text-[10px] text-right font-bold bg-white">
                        <option value="">كل الحالات</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>مستحق قريباً</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>تم السداد</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخر السداد</option>
                    </select>
                </form>
            </div>

            @if($installments->isEmpty())
                <div class="text-center py-16 text-gray-400 text-xs">
                    <i class="fas fa-calendar-check text-green-500 text-3xl mb-2 block"></i>
                    لا توجد أقساط سداد مستحقة حالياً.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-xs">
                        <thead class="bg-gray-50 text-gray-550 font-bold">
                            <tr>
                                <th class="px-6 py-3">رقم العقد</th>
                                <th class="px-6 py-3">العميل</th>
                                <th class="px-6 py-3">القسط رقم</th>
                                <th class="px-6 py-3">المبلغ</th>
                                <th class="px-6 py-3">تاريخ الاستحقاق</th>
                                <th class="px-6 py-3">الحالة</th>
                                <th class="px-6 py-3 text-left">تسجيل القبض</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($installments as $inst)
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-700 font-mono">
                                        <a href="{{ route('deals.show', $inst->deal) }}" class="hover:underline">#{{ $inst->deal->id }}</a>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $inst->deal->customer->name }}
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-gray-500">
                                        {{ $inst->installment_number == 0 ? 'دفعة مقدمة' : 'قسط #' . $inst->installment_number }}
                                    </td>
                                    <td class="px-6 py-4 font-bold font-mono text-gray-800">
                                        {{ number_format($inst->amount, 2) }} ر.س
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-500">
                                        {{ $inst->due_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold capitalize
                                            @if($inst->status === 'paid') bg-emerald-50 text-emerald-700
                                            @elseif($inst->status === 'overdue') bg-red-50 text-red-700 border border-red-100 animate-pulse
                                            @else bg-amber-50 text-amber-700 border border-amber-100 @endif">
                                            {{ $inst->status === 'paid' ? 'تم السداد' : ($inst->status === 'overdue' ? 'متأخر' : 'مستحق') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-left">
                                        @if($inst->status !== 'paid')
                                            <form action="{{ route('installments.pay', $inst) }}" method="POST" class="inline-flex gap-1">
                                                @csrf
                                                <input type="hidden" name="method" value="bank_transfer">
                                                <button type="submit" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-[10px] font-bold rounded-lg transition" onclick="return confirm('تأكيد استلام قيمة القسط وحفظ الدفعة؟')">
                                                    تحصيل سريع
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 font-mono text-[10px]">{{ $inst->paid_at->format('Y-m-d') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($installments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $installments->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Sales commissions panel (1/3 width) -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-right">
            <div class="px-6 py-4 border-b border-gray-150 bg-gray-50/50">
                <h3 class="text-xs font-bold text-gray-700"><i class="fas fa-percentage ml-2 text-indigo-500"></i>نسب وعمولات مناديب المبيعات</h3>
                <p class="text-[9px] text-gray-500 mt-1">العمولات المحتسبة من الصفقات المغلقة والمسلمة نهائياً</p>
            </div>

            <div class="p-6 space-y-4">
                @if(empty($commissions))
                    <div class="text-center py-12 text-gray-400 text-xs">
                        لم تسجل أية عوائد عمولات حتى الآن.
                    </div>
                @else
                    @foreach($commissions as $comm)
                        <div class="p-4 bg-gray-50/50 border border-gray-100 rounded-xl space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-extrabold text-indigo-950">{{ $comm['salesperson']->name }}</span>
                                <span class="text-[9px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg">
                                    {{ $comm['closed_count'] }} صفقات ناجحة
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-xs font-semibold">
                                <div>
                                    <span class="text-[9px] text-gray-400 block">حجم المبيعات</span>
                                    <span class="text-gray-800 font-bold font-mono">{{ number_format($comm['total_sales'], 2) }} ر.س</span>
                                </div>
                                <div>
                                    <span class="text-[9px] text-indigo-500 block">العمولة المستحقة</span>
                                    <span class="text-emerald-600 font-extrabold font-mono">{{ number_format($comm['commission'], 2) }} ر.س</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="bg-indigo-50/40 p-4 rounded-xl border border-indigo-100 text-[10px] text-indigo-900 leading-relaxed">
                    <i class="fas fa-info-circle ml-1"></i>
                    <strong>قواعد الاحتساب:</strong><br>
                    - صفقات الكاش: <strong>1.5%</strong><br>
                    - صفقات التمويل والمقايضة: <strong>2%</strong><br>
                    - صفقات التقسيط المعرض: <strong>3.5%</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
