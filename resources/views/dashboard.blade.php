@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="mb-8">
    <h3 class="text-3xl font-bold text-gray-700 text-right">لوحة التحكم التنفيذية</h3>
</div>

<!-- KPI Cards - Row 1 -->
<div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
    <!-- Total Suppliers -->
    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm border-r-4 border-blue-500 text-right">
        <div class="p-3 bg-blue-600 bg-opacity-10 rounded-full">
            <i class="fas fa-truck text-blue-600 text-2xl"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-2xl font-bold text-gray-700">{{ number_format($totalSuppliers) }}</h4>
            <div class="text-gray-500 font-medium">الموردون</div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm border-r-4 border-green-500 text-right">
        <div class="p-3 bg-green-600 bg-opacity-10 rounded-full">
            <i class="fas fa-boxes text-green-600 text-2xl"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-2xl font-bold text-gray-700">{{ number_format($totalProducts) }}</h4>
            <div class="text-gray-500 font-medium">المنتجات</div>
        </div>
    </div>

    <!-- Pending POs -->
    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm border-r-4 border-purple-500 text-right">
        <div class="p-3 bg-purple-600 bg-opacity-10 rounded-full">
            <i class="fas fa-file-invoice-dollar text-purple-600 text-2xl"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-2xl font-bold text-gray-700">{{ number_format($pendingPOs) }}</h4>
            <div class="text-gray-500 font-medium">أوامر الشراء المعلقة</div>
        </div>
    </div>
</div>

<!-- KPI Cards - Row 2 -->
<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
    <!-- Inventory Value -->
    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm border-r-4 border-orange-500 text-right">
        <div class="p-3 bg-orange-600 bg-opacity-10 rounded-full">
            <i class="fas fa-coins text-orange-600 text-2xl"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-2xl font-bold text-gray-700">{{ number_format($inventoryValue, 2) }} ر.س</h4>
            <div class="text-gray-500 font-medium">قيمة المخزون</div>
        </div>
    </div>

    <!-- Supplier Payables -->
    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm border-r-4 border-indigo-500 text-right">
        <div class="p-3 bg-indigo-600 bg-opacity-10 rounded-full">
            <i class="fas fa-money-bill-wave text-indigo-600 text-2xl"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-2xl font-bold text-gray-700">{{ number_format($totalPayables, 2) }} ر.س</h4>
            <div class="text-gray-500 font-medium">إجمالي المستحقات للموردين</div>
        </div>
    </div>

    <!-- Low Stock Count -->
    <div class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm border-r-4 border-red-500 text-right">
        <div class="p-3 bg-red-600 bg-opacity-10 rounded-full">
            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-2xl font-bold text-gray-700">{{ number_format($lowStockItems) }}</h4>
            <div class="text-red-600 font-bold">أصناف منخفضة المخزون</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-2">
    <!-- Monthly Purchases Chart -->
    <div class="p-6 bg-white rounded-md shadow-sm border-t-4 border-indigo-600 text-right">
        <h4 class="text-lg font-bold text-gray-700 mb-4">المشتريات الشهرية (آخر 6 أشهر)</h4>
        <div style="height: 300px; position: relative;">
            <canvas id="purchasesChart" height="300"></canvas>
        </div>
    </div>

    <!-- Inventory Movements Chart -->
    <div class="p-6 bg-white rounded-md shadow-sm border-t-4 border-blue-600 text-right">
        <h4 class="text-lg font-bold text-gray-700 mb-4">حركات المخزون اليومية (آخر 30 يوم)</h4>
        <div style="height: 300px; position: relative;">
            <canvas id="movementsChart" height="300"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">
    <!-- Recent POs Table (2/3 width) -->
    <div class="lg:col-span-2 bg-white rounded-md shadow-sm overflow-hidden border-t-4 border-gray-600 text-right">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="text-lg font-bold text-gray-700">أحدث أوامر الشراء</h4>
            <a href="{{ route('purchase-orders.index') }}" class="text-sm text-indigo-600 hover:underline">عرض الكل</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">رقم الأمر</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">المورد</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPOs as $po)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-700 font-bold">{{ $po->po_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $po->supplier->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs font-bold rounded-full 
                                {{ $po->status === 'received' ? 'bg-green-100 text-green-800' : ($po->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                @if($po->status === 'received') مستلم @elseif($po->status === 'approved') معتمد @else مسودة @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-bold">{{ number_format($po->total, 2) }} ر.س</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert Section (1/3 width) -->
    <div class="bg-white rounded-md shadow-sm overflow-hidden border-t-4 border-red-600 text-right">
        <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
            <h4 class="text-lg font-bold text-red-700">تنبيهات نقص المخزون</h4>
        </div>
        <div class="p-6">
            @if($lowStockProducts->count() > 0)
                <div class="space-y-4">
                    @foreach($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded border border-gray-200">
                            <div>
                                <div class="text-sm font-bold text-gray-800">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">رمز: {{ $product->sku }}</div>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-bold text-red-600">{{ $product->stock_qty }} <span class="text-xs font-normal text-gray-400">/ {{ $product->min_stock }}</span></div>
                                <div class="text-xs text-gray-400 uppercase">{{ $product->unit }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 italic">
                    <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                    <p>جميع مستويات المخزون جيدة.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Monthly Purchases Chart
        const purchasesCtx = document.getElementById('purchasesChart').getContext('2d');
        new Chart(purchasesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyPurchases->pluck('month')) !!},
                datasets: [{
                    label: 'المشتريات (ر.س)',
                    data: {!! json_encode($monthlyPurchases->pluck('total')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: {
                        rtl: true,
                        labels: { font: { family: 'Cairo' } }
                    }
                }
            }
        });

        // Inventory Movements Chart
        const movementsCtx = document.getElementById('movementsChart').getContext('2d');
        new Chart(movementsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($movementHistory->pluck('date')) !!},
                datasets: [{
                    label: 'الحركات',
                    data: {!! json_encode($movementHistory->pluck('count')) !!},
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.3,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: {
                        rtl: true,
                        labels: { font: { family: 'Cairo' } }
                    }
                }
            }
        });
    });
</script>
@endsection
