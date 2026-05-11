@extends('layouts.admin')

@section('title', 'تفاصيل المورد')

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
                    <a href="{{ route('suppliers.index') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">الموردون</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 text-xs mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $supplier->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex justify-between items-center mt-2 text-right">
        <h3 class="text-3xl font-bold text-gray-700">المورد: {{ $supplier->name }}</h3>
        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 font-bold">
            <i class="fas fa-arrow-right ml-2"></i> رجوع
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 text-right">
    <!-- Supplier Info -->
    <div class="bg-white shadow rounded-lg p-6 lg:col-span-1">
        <h4 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">معلومات الاتصال</h4>
        <div class="space-y-3">
            <p><span class="font-bold text-gray-600">البريد الإلكتروني:</span> {{ $supplier->email ?? 'غير متوفر' }}</p>
            <p><span class="font-bold text-gray-600">الهاتف:</span> {{ $supplier->phone ?? 'غير متوفر' }}</p>
            <p><span class="font-bold text-gray-600">العنوان:</span><br>{{ $supplier->address ?? 'غير متوفر' }}</p>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="bg-white shadow rounded-lg p-6 lg:col-span-1 flex flex-col items-center justify-center">
        <h4 class="text-xl font-bold mb-2 text-gray-800">الرصيد الحالي</h4>
        <div class="text-4xl font-black {{ $supplier->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
            {{ number_format($supplier->balance, 2) }} ر.س
        </div>
        <p class="text-gray-500 mt-2 text-sm text-center">المبلغ المستحق لهذا المورد</p>
    </div>

    <!-- Stats Card -->
    <div class="bg-white shadow rounded-lg p-6 lg:col-span-1">
        <h4 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">ملخص المشتريات</h4>
        <div class="space-y-3">
            <p><span class="font-bold text-gray-600">إجمالي الأوامر:</span> {{ $supplier->purchaseOrders->count() }}</p>
            <p><span class="font-bold text-gray-600">آخر أمر شراء:</span> {{ $supplier->purchaseOrders->last()?->po_number ?? 'لا يوجد' }}</p>
        </div>
    </div>
</div>

<!-- PO History -->
<div class="bg-white shadow rounded-lg overflow-hidden text-right">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h4 class="text-xl font-bold text-gray-800">سجل أوامر الشراء</h4>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase bg-gray-50">رقم الأمر</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase bg-gray-50">التاريخ</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase bg-gray-50">الحالة</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase bg-gray-50">الإجمالي</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase bg-gray-50">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($supplier->purchaseOrders as $po)
            <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $po->po_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $po->created_at->format('Y-m-d') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($po->status === 'draft')
                        <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-800 rounded-full">مسودة</span>
                    @elseif($po->status === 'approved')
                        <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full">معتمد</span>
                    @elseif($po->status === 'received')
                        <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">مستلم</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ number_format($po->total, 2) }} ر.س</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('purchase-orders.show', $po) }}" class="text-indigo-600 hover:text-indigo-900"><i class="fas fa-eye ml-1"></i> عرض</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">لا توجد أوامر شراء حالياً.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
