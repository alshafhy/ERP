@extends('layouts.admin')

@section('title', 'تفاصيل أمر الشراء')

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
                    <a href="{{ route('purchase-orders.index') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">أوامر الشراء</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-left text-gray-400 text-xs mx-2"></i>
                    <span class="text-sm font-medium text-gray-500">{{ $purchaseOrder->po_number }}</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="flex justify-between items-center mt-2 text-right">
        <h3 class="text-3xl font-bold text-gray-700">الأمر: {{ $purchaseOrder->po_number }}</h3>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 font-bold">
                <i class="fas fa-arrow-right ml-2"></i> رجوع
            </a>
            
            @if($purchaseOrder->status === 'draft')
                <form action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow transition duration-150 font-bold">
                        <i class="fas fa-check ml-2"></i> اعتماد الأمر
                    </button>
                </form>
            @elseif($purchaseOrder->status === 'approved')
                <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 shadow transition duration-150 font-bold">
                        <i class="fas fa-download ml-2"></i> استلام الطلب
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-right">
    <div class="bg-white p-6 shadow rounded-lg border-t-4 border-indigo-500">
        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">المورد</h4>
        <p class="text-lg font-bold text-gray-800">{{ $purchaseOrder->supplier->name }}</p>
        <p class="text-gray-600 text-sm">{{ $purchaseOrder->supplier->email }}</p>
        <p class="text-gray-600 text-sm">{{ $purchaseOrder->supplier->phone }}</p>
    </div>
    <div class="bg-white p-6 shadow rounded-lg border-t-4 border-blue-500">
        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">معلومات الأمر</h4>
        <p class="text-sm text-gray-800"><span class="font-bold">التاريخ:</span> {{ $purchaseOrder->created_at->format('Y-m-d') }}</p>
        <div class="text-sm text-gray-800 mt-1 flex items-center">
            <span class="font-bold ml-2">الحالة:</span>
            @if($purchaseOrder->status === 'draft')
                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-800 rounded-full">مسودة</span>
            @elseif($purchaseOrder->status === 'approved')
                <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-800 rounded-full">معتمد</span>
            @elseif($purchaseOrder->status === 'received')
                <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">مستلم</span>
            @endif
        </div>
        @if($purchaseOrder->received_at)
            <p class="text-sm text-gray-800 mt-1"><span class="font-bold">تاريخ الاستلام:</span> {{ \Carbon\Carbon::parse($purchaseOrder->received_at)->format('Y-m-d H:i') }}</p>
        @endif
    </div>
    <div class="bg-white p-6 shadow rounded-lg border-t-4 border-green-500">
        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">الإجماليات</h4>
        <div class="space-y-1">
            <p class="text-sm text-gray-800 flex justify-between"><span>المجموع الفرعي:</span> <span>{{ number_format($purchaseOrder->subtotal, 2) }} ر.س</span></p>
            <p class="text-sm text-gray-800 flex justify-between"><span>الضريبة (15%):</span> <span>{{ number_format($purchaseOrder->tax, 2) }} ر.س</span></p>
            <div class="border-t mt-2 pt-2 flex justify-between">
                <span class="font-bold text-gray-700">الإجمالي النهائي:</span>
                <span class="text-xl font-black text-indigo-700">{{ number_format($purchaseOrder->total, 2) }} ر.س</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden text-right">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h4 class="text-xl font-bold text-gray-800">أصناف الطلب</h4>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right tracking-wider">المنتج</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right tracking-wider">الكمية</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right tracking-wider">سعر الوحدة</th>
                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right tracking-wider">الإجمالي</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($purchaseOrder->items as $item)
            <tr class="hover:bg-gray-50 transition-colors {{ $loop->even ? 'bg-gray-50/30' : '' }}">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900">{{ $item->product->name }}</div>
                    <div class="text-xs text-gray-500">رمز: {{ $item->product->sku }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700 font-bold">{{ $item->qty }}</td>
                <td class="px-6 py-4 text-sm text-gray-700 font-bold">{{ number_format($item->unit_price, 2) }} ر.س</td>
                <td class="px-6 py-4 text-sm font-black text-gray-800">{{ number_format($item->total, 2) }} ر.س</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
