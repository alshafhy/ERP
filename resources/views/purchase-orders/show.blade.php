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
                    <span class="text-sm font-medium text-gray-500">PO-{{ str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="flex justify-between items-center mt-2 text-right">
        <h3 class="text-3xl font-bold text-gray-700">الأمر: PO-{{ str_pad($purchaseOrder->id, 5, '0', STR_PAD_LEFT) }}</h3>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-150 font-bold">
                <i class="fas fa-arrow-right ml-2"></i> رجوع
            </a>
            
            @if(!$purchaseOrder->delivered_at)
                <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 shadow transition duration-150 font-bold">
                        <i class="fas fa-download ml-2"></i> تأكيد استلام الطلب والسيارة
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@if (session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 text-right" role="alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 text-right" role="alert">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-right">
    <div class="bg-white p-6 shadow rounded-lg border-t-4 border-indigo-500">
        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">المورد</h4>
        <p class="text-lg font-bold text-gray-800">{{ $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-' }}</p>
        <p class="text-gray-600 text-sm mt-1">{{ $purchaseOrder->supplier ? $purchaseOrder->supplier->contact : '' }}</p>
    </div>
    <div class="bg-white p-6 shadow rounded-lg border-t-4 border-blue-500">
        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">معلومات الأمر</h4>
        <p class="text-sm text-gray-800"><span class="font-bold">تاريخ الشراء:</span> {{ $purchaseOrder->purchased_at ? $purchaseOrder->purchased_at->format('Y-m-d') : ($purchaseOrder->created_at ? $purchaseOrder->created_at->format('Y-m-d') : '-') }}</p>
        <div class="text-sm text-gray-800 mt-1 flex items-center">
            <span class="font-bold ml-2">الحالة:</span>
            @if($purchaseOrder->delivered_at)
                <span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-800 rounded-full">مستلم</span>
            @else
                <span class="px-2 py-1 text-xs font-bold bg-yellow-100 text-yellow-800 rounded-full">تحت الشحن والتسليم</span>
            @endif
        </div>
        @if($purchaseOrder->delivered_at)
            <p class="text-sm text-gray-800 mt-1"><span class="font-bold">تاريخ الاستلام:</span> {{ $purchaseOrder->delivered_at->format('Y-m-d H:i') }}</p>
        @endif
    </div>
    <div class="bg-white p-6 shadow rounded-lg border-t-4 border-green-500">
        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">القيمة والمالية</h4>
        <div class="space-y-1">
            <div class="border-t mt-2 pt-2 flex justify-between">
                <span class="font-bold text-gray-700">سعر الشراء النهائي:</span>
                <span class="text-xl font-black text-indigo-700">{{ number_format($purchaseOrder->purchase_price, 2) }} ر.س</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden text-right">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h4 class="text-xl font-bold text-gray-800">السيارة المشتراة المرتبطة بالأمر</h4>
    </div>
    <div class="p-6">
        @if($purchaseOrder->vehicle)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="text-sm font-bold text-gray-500 uppercase mb-1">السيارة</h5>
                    <p class="text-lg font-bold text-gray-900">{{ $purchaseOrder->vehicle->make }} {{ $purchaseOrder->vehicle->model }} ({{ $purchaseOrder->vehicle->year }})</p>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-gray-500 uppercase mb-1">رقم الهيكل (VIN)</h5>
                    <p class="text-lg font-mono font-bold text-gray-900">{{ $purchaseOrder->vehicle->vin }}</p>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-gray-500 uppercase mb-1">اللون والمواصفات</h5>
                    <p class="text-sm text-gray-700">خارجي: {{ $purchaseOrder->vehicle->color_exterior }} / داخلي: {{ $purchaseOrder->vehicle->color_interior }}</p>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-gray-500 uppercase mb-1">الحالة في المخزون</h5>
                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full {{ $purchaseOrder->vehicle->status === 'available' ? 'bg-green-100 text-green-800' : ($purchaseOrder->vehicle->status === 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ $purchaseOrder->vehicle->status === 'available' ? 'متاحة للبيع' : ($purchaseOrder->vehicle->status === 'sold' ? 'مباعة' : $purchaseOrder->vehicle->status) }}
                    </span>
                </div>
            </div>
        @else
            <p class="text-gray-500">لا توجد سيارة مرتبطة بهذا الأمر أو تم حذفها.</p>
        @endif
    </div>
</div>
@endsection
