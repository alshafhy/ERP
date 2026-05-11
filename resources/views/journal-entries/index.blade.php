@extends('layouts.admin')

@section('title', 'القيود المحاسبية')

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
                    <span class="text-sm font-medium text-gray-500">القيود المحاسبية</span>
                </div>
            </li>
        </ol>
    </nav>
    <div class="flex justify-between items-center mt-2 text-right">
        <h3 class="text-3xl font-bold text-gray-700">القيود المحاسبية</h3>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden text-right">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">رقم القيد</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">التاريخ</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">البيان</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">حساب المدين</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">حساب الدائن</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">المبلغ</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($entries as $entry)
            <tr class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-900">
                    {{ $entry->entry_number }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $entry->created_at->format('Y-m-d') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 font-bold">
                    {{ $entry->description }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                    {{ $entry->debit_account }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                    {{ $entry->credit_account }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-indigo-700">
                    {{ number_format($entry->amount, 2) }} ر.س
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $entries->links() }}
    </div>
</div>
@endsection
