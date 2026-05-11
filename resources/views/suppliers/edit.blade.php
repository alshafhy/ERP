@extends('layouts.admin')

@section('title', 'تعديل مورد')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 text-right">
        <h3 class="text-3xl font-bold text-gray-700">تعديل مورد: {{ $supplier->name }}</h3>
        <a href="{{ route('suppliers.index') }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 text-right">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">الاسم</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="name" name="name" type="text" required value="{{ old('name', $supplier->name) }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">البريد الإلكتروني</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="email" name="email" type="email" value="{{ old('email', $supplier->email) }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">الهاتف</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="phone" name="phone" type="text" value="{{ old('phone', $supplier->phone) }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="balance">الرصيد</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="balance" name="balance" type="number" step="0.01" value="{{ old('balance', $supplier->balance) }}">
                </div>
                <div class="mb-4 md:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">العنوان</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" id="address" name="address" rows="3">{{ old('address', $supplier->address) }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end mt-6">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                    تحديث البيانات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
