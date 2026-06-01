@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-black text-gray-800 text-right">لوحة التحكم التنفيذية - معرض السيارات</h1>
        <p class="text-xs text-gray-500 text-right mt-1">إحصائيات المبيعات، حالة المخزون، ونظام التنبيهات لمتابعة العملاء</p>
    </div>

    <!-- KPI Row 1: Vehicles Stock -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Vehicles -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fas fa-car text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-black text-gray-800">{{ number_format($totalVehicles) }}</h4>
                <div class="text-gray-400 text-xs mt-0.5">إجمالي السيارات</div>
            </div>
        </div>

        <!-- Available Vehicles -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-black text-gray-800">{{ number_format($availableVehicles) }}</h4>
                <div class="text-gray-400 text-xs mt-0.5">سيارات متاحة للبيع</div>
            </div>
        </div>

        <!-- Reserved Vehicles -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i class="fas fa-hourglass-half text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-black text-gray-800">{{ number_format($reservedVehicles) }}</h4>
                <div class="text-gray-400 text-xs mt-0.5">سيارات محجوزة</div>
            </div>
        </div>

        <!-- Sold Vehicles -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-gray-50 text-gray-500 rounded-xl">
                <i class="fas fa-handshake text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-black text-gray-800">{{ number_format($soldVehicles) }}</h4>
                <div class="text-gray-400 text-xs mt-0.5">سيارات مباعة</div>
            </div>
        </div>
    </div>

    <!-- KPI Row 2: Financials & CRM -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Inventory financial value -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i class="fas fa-coins text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-xl font-black text-gray-800">{{ number_format($inventoryValue, 2) }} <span class="text-xs font-normal">ريال</span></h4>
                <div class="text-gray-400 text-xs mt-0.5">القيمة المالية للمخزون الحالي</div>
            </div>
        </div>

        <!-- Total CRM opportunities -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fas fa-filter text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-black text-gray-800">{{ number_format($totalLeads) }}</h4>
                <div class="text-gray-400 text-xs mt-0.5">إجمالي الفرص المكتشفة</div>
            </div>
        </div>

        <!-- Active Opportunities -->
        <div class="flex items-center px-5 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 text-right">
            <div class="p-3 bg-sky-50 text-sky-600 rounded-xl">
                <i class="fas fa-funnel-dollar text-2xl"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-black text-gray-800">{{ number_format($activeLeads) }}</h4>
                <div class="text-gray-400 text-xs mt-0.5">الفرص النشطة والمفتوحة</div>
            </div>
        </div>
    </div>

    <!-- Overdue Follow-up Reminder System (Follow-up Reminder panel) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Reminder Panel (Flashing/Notice for agents) -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
            <div class="p-6 pb-4 border-b border-gray-100 bg-red-50/50">
                <div class="flex items-center gap-2">
                    <span class="animate-ping inline-flex h-2.5 w-2.5 rounded-full bg-red-400 opacity-75"></span>
                    <h3 class="text-sm font-bold text-red-800"><i class="fas fa-bell ml-1"></i>تنبيهات المتابعة المتأخرة</h3>
                </div>
                <p class="text-[10px] text-red-600 mt-1">فرص بيعية تجاوزت موعد المتابعة المتفق عليه دون إغلاق</p>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto max-h-[400px] space-y-4">
                @if($overdueFollowUps->isEmpty())
                    <div class="text-center py-12 text-gray-400 text-xs">
                        <i class="fas fa-calendar-check text-green-500 text-3xl mb-2 block"></i>
                        جميع مواعيد المتابعة محدثة ومستقرة!
                    </div>
                @else
                    @foreach($overdueFollowUps as $lead)
                        <div class="p-4 bg-red-50/40 border border-red-100 rounded-xl space-y-2.5">
                            <div class="flex justify-between items-start">
                                <a href="{{ route('leads.show', $lead) }}" class="text-xs font-black text-red-900 hover:underline">
                                    {{ $lead->customer->name }}
                                </a>
                                <span class="text-[9px] font-bold text-red-700 bg-red-100 px-1.5 py-0.5 rounded-md">
                                    منذ {{ $lead->follow_up_at->diffForHumans() }}
                                </span>
                            </div>
                            @if($lead->vehicle)
                                <div class="text-[9px] font-bold text-gray-500 bg-white px-2 py-0.5 rounded-md border border-gray-100 inline-block">
                                    سيارة: {{ $lead->vehicle->make }} {{ $lead->vehicle->model }}
                                </div>
                            @endif
                            <p class="text-[10px] text-gray-600 line-clamp-2 leading-relaxed">{{ $lead->notes }}</p>
                            <a href="{{ route('leads.show', $lead) }}" class="inline-flex items-center text-[10px] font-bold text-red-700 hover:underline">
                                إدارة المتابعة وإجراء الاتصال <i class="fas fa-angle-left mr-1"></i>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Recent Deals table (2/3 width) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-right">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-handshake ml-2 text-indigo-500"></i>أحدث الصفقات المسجلة</h3>
                <span class="text-xs text-gray-400">آخر صفقات مغلقة</span>
            </div>
            
            @if($recentDeals->isEmpty())
                <div class="text-center py-16 text-gray-400 text-xs">
                    لم تسجل أية صفقات شراء كاملة حتى الآن.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead class="bg-gray-50 text-xs font-bold text-gray-555">
                            <tr>
                                <th class="px-6 py-3">العميل</th>
                                <th class="px-6 py-3">السيارة</th>
                                <th class="px-6 py-3">نوع الدفع</th>
                                <th class="px-6 py-3">سعر الاتفاق</th>
                                <th class="px-6 py-3">الحالة المالية</th>
                                <th class="px-6 py-3">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDeals as $deal)
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $deal->customer->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $deal->vehicle->make ?? 'N/A' }} {{ $deal->vehicle->model ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 uppercase">
                                            {{ $deal->deal_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-800 font-mono">
                                        {{ number_format($deal->agreed_price, 2) }} ر.س
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                            {{ $deal->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400 font-mono">
                                        {{ $deal->created_at->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
