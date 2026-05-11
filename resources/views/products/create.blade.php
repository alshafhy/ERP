@extends('layouts.admin')

@section('title', 'إضافة منتج')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 text-right">
        <h3 class="text-3xl font-bold text-gray-700">إضافة منتج جديد</h3>
        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 text-right">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">اسم المنتج</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="name" name="name" type="text" required value="{{ old('name') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="sku">الرمز (SKU)</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="sku" name="sku" type="text" required value="{{ old('sku') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">الفئة</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="category" name="category" type="text" value="{{ old('category') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="unit">الوحدة (مثلاً: قطعة، كيلو)</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="unit" name="unit" type="text" value="{{ old('unit', 'قطعة') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cost_price">سعر التكلفة</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="cost_price" name="cost_price" type="number" step="0.01" required value="{{ old('cost_price') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="sell_price">سعر البيع</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="sell_price" name="sell_price" type="number" step="0.01" required value="{{ old('sell_price') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="min_stock">حد المخزون الأدنى</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="min_stock" name="min_stock" type="number" step="0.01" value="{{ old('min_stock', 0) }}">
                </div>
            </div>
            <div class="flex items-center justify-end mt-6">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                    حفظ المنتج
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
