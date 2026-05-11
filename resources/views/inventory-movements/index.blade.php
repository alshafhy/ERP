@extends('layouts.admin')

@section('title', 'حركات المخزون')

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
                    <span class="text-sm font-medium text-gray-500">حركات المخزون</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex justify-between items-center mt-2 text-right">
        <h3 class="text-3xl font-bold text-gray-700">حركات المخزون</h3>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden text-right">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">التاريخ</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">المنتج</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">النوع</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الكمية</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الرصيد بعد الحركة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">المرجع</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($movements as $movement)
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $movement->created_at->format('Y-m-d H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">{{ $movement->product->name }}</div>
                    <div class="text-xs text-gray-500">رمز: {{ $movement->product->sku }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full {{ $movement->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $movement->type === 'in' ? 'وارد' : 'صادر' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }} font-black">
                    {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->qty }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-black">
                    {{ $movement->balance_after }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($movement->reference_type)
                        {{ $movement->reference_type === 'purchase_order' ? 'أمر شراء' : (str_contains($movement->reference_type, '\\') ? class_basename($movement->reference_type) : $movement->reference_type) }} #{{ $movement->reference_id }}
                    @else
                        لا يوجد
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $movements->links() }}
    </div>
</div>
@endsection
