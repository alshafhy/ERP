@extends('layouts.admin')

@section('title', 'أوامر الشراء')

@section('content')
<div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-home ml-2"></i> لوحة التحكم
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 text-xs mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">أوامر الشراء</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex justify-between items-center mt-2 text-right">
        <h3 class="text-3xl font-bold text-gray-700">أوامر الشراء</h3>
        <a href="{{ route('purchase-orders.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow font-bold">
            <i class="fas fa-plus ml-2"></i> إنشاء أمر شراء
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden text-right">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">رقم الأمر</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">المورد</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">السيارة المشتراة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">التاريخ</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">قيمة الشراء</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($purchaseOrders as $po)
            <tr class="hover:bg-gray-50 transition-colors duration-200 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                    PO-{{ str_pad($po->id, 5, '0', STR_PAD_LEFT) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $po->supplier ? $po->supplier->name : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                    @if($po->vehicle)
                        {{ $po->vehicle->make }} {{ $po->vehicle->model }} ({{ $po->vehicle->year }})
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $po->purchased_at ? $po->purchased_at->format('Y-m-d') : ($po->created_at ? $po->created_at->format('Y-m-d') : '-') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-700">
                    {{ number_format($po->purchase_price, 2) }} ر.س
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($po->delivered_at)
                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">مستلم</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">في الشحن</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('purchase-orders.show', $po) }}" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-eye ml-1"></i> عرض وتأكيد الاستلام
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                    لا توجد أوامر شراء حالياً
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $purchaseOrders->links() }}
    </div>
</div>
@endsection
