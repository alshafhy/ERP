@extends('layouts.admin')

@section('title', 'الموردون')

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
                    <span class="text-sm font-medium text-gray-500">الموردون</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex justify-between items-center mt-2">
        <h3 class="text-3xl font-bold text-gray-700">الموردون</h3>
        <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow">
            <i class="fas fa-plus ml-2"></i> إضافة مورد
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">اسم المورد</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الهاتف</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الرصيد</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($suppliers as $supplier)
            <tr class="hover:bg-gray-50 transition-colors duration-200 {{ $loop->even ? 'bg-gray-50/50' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">{{ $supplier->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $supplier->phone }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $supplier->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full {{ $supplier->balance > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ number_format($supplier->balance, 2) }} ر.س
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('suppliers.show', $supplier) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-900 ml-4">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('هل أنت متأكد؟')">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
