@extends('layouts.admin')

@section('title', 'إنشاء أمر شراء')

@section('content')
<div class="max-w-4xl mx-auto text-right">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-bold text-gray-700">أمر شراء جديد</h3>
        <a href="{{ route('purchase-orders.index') }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="supplier_id">المورد</label>
                    <select name="supplier_id" id="supplier_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" required>
                        <option value="">اختر المورد</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} (الرصيد: {{ number_format($supplier->balance, 2) }} ر.س)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="vehicle_id">السيارة المشتراة</label>
                    <select name="vehicle_id" id="vehicle_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" required>
                        <option value="">اختر السيارة</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }}) - رقم الهيكل: {{ $vehicle->vin }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purchase_price">قيمة الشراء (ر.س)</label>
                    <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" placeholder="0.00" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purchased_at">تاريخ الشراء</label>
                    <input type="date" name="purchased_at" id="purchased_at" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 border-t pt-4">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-8 rounded-lg shadow-lg focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                    حفظ أمر الشراء
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
