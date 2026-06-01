@extends('layouts.admin')

@section('title', 'التقارير ولوحات التحليل')

@section('content')
<div class="space-y-6">
    <!-- Header with Date Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-right">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">مركز التقارير ولوحات الأداء</h1>
            <p class="text-sm text-gray-500 mt-1">تتبع كفاءة المبيعات، ومعدلات التحويل، وحالة المخزون، وتحليل القنوات البيعية</p>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-end gap-3 font-bold text-xs text-gray-650">
            <div>
                <label class="block text-[10px] text-gray-400 mb-1">من تاريخ</label>
                <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="py-1.5 px-3 border border-gray-200 rounded-lg text-right">
            </div>
            <div>
                <label class="block text-[10px] text-gray-400 mb-1">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="py-1.5 px-3 border border-gray-200 rounded-lg text-right">
            </div>
            <div>
                <button type="submit" class="py-1.5 px-4 bg-indigo-600 hover:bg-indigo-750 text-white rounded-lg transition">
                    تحديث الفلترة 📊
                </button>
            </div>
        </form>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Stock KPI -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2 text-right">
            <span class="text-xs text-gray-400 block"><i class="fas fa-warehouse ml-1.5 text-blue-500"></i>المخزون وحركة المبيعات</span>
            <div class="flex justify-between items-baseline">
                <h4 class="text-2xl font-black text-gray-800 font-mono">{{ $totalInStock }} <span class="text-xs font-normal text-gray-500">متاحة</span></h4>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">+{{ $soldThisMonth }} مباعة</span>
            </div>
            <p class="text-[9px] text-gray-500">السيارات المباعة خلال الفترة المحددة</p>
        </div>

        <!-- Revenue / Outstanding KPI -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2 text-right">
            <span class="text-xs text-gray-400 block"><i class="fas fa-coins ml-1.5 text-emerald-500"></i>الإيرادات والمستحقات المعلقة</span>
            <div class="flex flex-col space-y-1">
                <h4 class="text-lg font-black text-emerald-600 font-mono">{{ number_format($revenueCollected, 2) }} <span class="text-[10px] font-normal text-gray-500">ر.س محصل</span></h4>
                <span class="text-xs font-bold font-mono text-amber-600">المعلق: {{ number_format($outstandingPayments, 2) }} ر.س</span>
            </div>
        </div>

        <!-- Conversion KPI -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-2 text-right">
            <span class="text-xs text-gray-400 block"><i class="fas fa-filter ml-1.5 text-indigo-500"></i>العملاء ومعدلات التحويل</span>
            <div class="flex justify-between items-baseline">
                <h4 class="text-2xl font-black text-indigo-600 font-mono">{{ $conversionRate }}%</h4>
                <span class="text-[10px] font-bold text-gray-500 bg-gray-50 px-2 py-0.5 rounded-lg">+{{ $newLeadsThisWeek }} عميل جديد هذا الأسبوع</span>
            </div>
            <p class="text-[9px] text-gray-500">معدل تحويل العملاء المهتمين إلى صفقات</p>
        </div>

        <!-- Top Agent KPI -->
        <div class="bg-indigo-50/40 p-6 rounded-2xl border border-indigo-100 space-y-2 text-right">
            <span class="text-xs text-indigo-900 block font-bold"><i class="fas fa-trophy ml-1.5 text-amber-500"></i>مندوب المبيعات الأعلى أداءً 🏆</span>
            @if($topSalesperson)
                <h4 class="text-xs font-extrabold text-indigo-950">{{ $topSalesperson['user']->name }}</h4>
                <div class="flex justify-between items-center text-[10px] text-indigo-800 font-bold font-mono">
                    <span>{{ $topSalesperson['deals_count'] }} صفقات ناجحة</span>
                    <span>{{ number_format($topSalesperson['total_sales'], 2) }} ر.س</span>
                </div>
            @else
                <p class="text-xs text-gray-400">لا توجد صفقات منتهية في الفترة المحددة</p>
            @endif
        </div>
    </div>

    <!-- Analytical Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Sales Bar Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-right space-y-4">
            <h3 class="text-xs font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-chart-bar ml-1.5 text-indigo-500"></i>معدل المبيعات الشهرية لآخر 6 شهور</h3>
            <div class="h-64">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>

        <!-- Lead status funnel chart -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-right space-y-4">
            <h3 class="text-xs font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-funnel-dollar ml-1.5 text-indigo-500"></i>قمع الحالات ومراحل الاهتمام بالعملاء</h3>
            <div class="h-64">
                <canvas id="leadsFunnelChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Reports Tables & Export Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-right">
        <!-- Tabbed Menu Header -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-150 flex justify-between items-center flex-wrap gap-4">
            <div class="flex gap-4 font-bold text-xs text-gray-600">
                <span class="text-indigo-600 pb-1 border-b-2 border-indigo-600 cursor-pointer"><i class="fas fa-handshake ml-1.5"></i>تقرير المبيعات والصفقات</span>
            </div>
            
            <a href="{{ route('reports.sales.export', request()->all()) }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition gap-1.5">
                <i class="fas fa-file-excel"></i>
                <span>تصدير تقرير المبيعات إلى Excel (xlsx) 📥</span>
            </a>
        </div>

        <!-- 1. Sales Report Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse text-xs">
                <thead class="bg-gray-50/50 text-gray-500">
                    <tr>
                        <th class="px-6 py-3">رقم الصفقة</th>
                        <th class="px-6 py-3">العميل</th>
                        <th class="px-6 py-3">المركبة</th>
                        <th class="px-6 py-3">طريقة السداد</th>
                        <th class="px-6 py-3">قيمة العقد</th>
                        <th class="px-6 py-3">مندوب المبيعات</th>
                        <th class="px-6 py-3">الفرع</th>
                        <th class="px-6 py-3">حالة العقد</th>
                    </tr>
                </thead>
                <tbody>
                    @if($salesDeals->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-400">لا توجد صفقات مغلقة في هذه الفترة للتقرير</td>
                        </tr>
                    @else
                        @foreach($salesDeals as $deal)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-6 py-3.5 font-bold font-mono text-gray-700">#{{ $deal->id }}</td>
                                <td class="px-6 py-3.5 font-bold text-gray-800">{{ $deal->customer->name }}</td>
                                <td class="px-6 py-3.5 text-gray-600">{{ $deal->vehicle->make }} {{ $deal->vehicle->model }}</td>
                                <td class="px-6 py-3.5 capitalize text-gray-600">{{ $deal->deal_type }}</td>
                                <td class="px-6 py-3.5 font-extrabold font-mono text-indigo-650">{{ number_format($deal->final_price, 2) }} ر.س</td>
                                <td class="px-6 py-3.5 text-gray-700">{{ $deal->salesperson->name }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex px-2 py-0.5 rounded bg-gray-50 text-[10px] font-bold text-gray-650">{{ $deal->branch ? $deal->branch->name : 'إدارة سيادية' }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $deal->status === 'closed' ? 'مغلق ومؤرشف' : 'مسلم للعميل' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inventory Stock Age & Lead Channel reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-right">
        <!-- Stock Aging / Inventory Report -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-150 flex justify-between items-center">
                <h3 class="text-xs font-bold text-gray-700"><i class="fas fa-cubes ml-1.5 text-indigo-500"></i>تقرير أعمار المخزون المتوفر والمسعر</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-[11px]">
                    <thead class="bg-gray-50/50 text-gray-500">
                        <tr>
                            <th class="px-4 py-2.5">السيارة</th>
                            <th class="px-4 py-2.5">أيام الركود بالمخزن</th>
                            <th class="px-4 py-2.5">سعر التكلفة</th>
                            <th class="px-4 py-2.5">السعر المعروض</th>
                            <th class="px-4 py-2.5">الفرع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryVehicles as $veh)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $veh->make }} {{ $veh->model }} ({{ $veh->year }})</td>
                                <td class="px-4 py-3 font-bold font-mono">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                        @if($veh->days_in_stock > 90) bg-red-50 text-red-700 border border-red-100
                                        @elseif($veh->days_in_stock > 30) bg-amber-50 text-amber-700 border border-amber-100
                                        @else bg-green-50 text-green-700 @endif">
                                        {{ $veh->days_in_stock }} يوم
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-500">{{ number_format($veh->cost_price, 2) }} ر.س</td>
                                <td class="px-4 py-3 font-mono font-bold text-indigo-650">{{ number_format($veh->listing_price, 2) }} ر.س</td>
                                <td class="px-4 py-3 text-gray-600">{{ $veh->branch ? $veh->branch->name : 'مخزن رئيسي' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lead source funnel conversion rate report -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-150 flex justify-between items-center">
                <h3 class="text-xs font-bold text-gray-700"><i class="fas fa-network-wired ml-1.5 text-indigo-500"></i>تقرير قنوات الاهتمام ومعدلات تحويل العملاء</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-[11px]">
                    <thead class="bg-gray-50/50 text-gray-550">
                        <tr>
                            <th class="px-6 py-3 font-bold">قناة الوصول (Source Channel)</th>
                            <th class="px-6 py-3 font-bold">إجمالي العملاء المهتمين</th>
                            <th class="px-6 py-3 font-bold">الصفقات المغلقة الناجحة</th>
                            <th class="px-6 py-3 font-bold">كفاءة تحويل القناة البيعية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leadSourceStats as $stat)
                            @php
                                $percent = $stat->total > 0 ? round(($stat->converted / $stat->total) * 100, 1) : 0;
                            @endphp
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-extrabold capitalize text-gray-750">
                                    @if($stat->source === 'website') <i class="fas fa-globe ml-2 text-indigo-500"></i>الموقع الإلكتروني
                                    @elseif($stat->source === 'call') <i class="fas fa-phone-alt ml-2 text-emerald-500"></i>اتصال هاتفي مباشر
                                    @elseif($stat->source === 'walk_in') <i class="fas fa-walking ml-2 text-amber-500"></i>زيارة مباشرة للمعرض
                                    @else <i class="fas fa-share-alt ml-2 text-purple-500"></i>ترشيح خارجي / عميل مسوق
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-600">{{ $stat->total }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-emerald-600">{{ $stat->converted }}</td>
                                <td class="px-6 py-4 font-bold font-mono text-indigo-650">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 bg-gray-100 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span>{{ $percent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Monthly Sales Chart
        const salesCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesMonths) !!},
                datasets: [{
                    label: 'إيرادات المبيعات المحققة (ر.س)',
                    data: {!! json_encode($salesValues) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.85)',
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: { family: 'Cairo, sans-serif' }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'monospace' }
                        }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Cairo, sans-serif' }
                        }
                    }
                }
            }
        });

        // 2. Leads Funnel Chart
        const leadsCtx = document.getElementById('leadsFunnelChart').getContext('2d');
        new Chart(leadsCtx, {
            type: 'doughnut',
            data: {
                labels: ['جديد (New)', 'تم التواصل', 'مؤهل للشراء', 'غير مهتم (Lost)', 'تم التحويل لصفقة 🎉'],
                datasets: [{
                    data: {!! json_encode($funnelData) !!},
                    backgroundColor: [
                        '#3b82f6', // blue
                        '#eab308', // yellow
                        '#8b5cf6', // purple
                        '#ef4444', // red
                        '#10b981'  // green
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Cairo, sans-serif' }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
