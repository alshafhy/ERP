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
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">المركبة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة السابقة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة الجديدة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">بواسطة</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الملاحظات والبيان</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($movements as $movement)
            @php
                $statusMap = [
                    'available' => 'متاحة',
                    'in_transit' => 'في الشحن',
                    'reserved' => 'محجوزة',
                    'sold' => 'مباعة',
                ];
                $statusFromAr = $statusMap[$movement->status_from] ?? $movement->status_from;
                $statusToAr = $statusMap[$movement->status_to] ?? $movement->status_to;
            @endphp
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $movement->changed_at ? $movement->changed_at->format('Y-m-d H:i') : ($movement->created_at ? $movement->created_at->format('Y-m-d H:i') : '-') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($movement->vehicle)
                        <div class="text-sm font-bold text-gray-900">{{ $movement->vehicle->make }} {{ $movement->vehicle->model }} ({{ $movement->vehicle->year }})</div>
                        <div class="text-xs text-gray-500">رقم الهيكل: {{ $movement->vehicle->vin }}</div>
                    @else
                        <div class="text-sm text-gray-400">مركبة محذوفة</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">
                        {{ $statusFromAr }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full {{ $movement->status_to === 'available' ? 'bg-green-100 text-green-800' : ($movement->status_to === 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ $statusToAr }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $movement->user ? $movement->user->name : 'النظام' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    {{ $movement->notes }}
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
