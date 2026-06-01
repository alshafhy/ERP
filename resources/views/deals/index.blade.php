@extends('layouts.admin')

@section('title', 'إدارة عقود المبيعات والصفقات')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">عقود المبيعات والصفقات</h1>
            <p class="text-sm text-gray-500 mt-1">تسجيل وإدارة فواتير المبيعات، خطط التمويل وحالات تسليم السيارات للمشترين</p>
        </div>
        <a href="{{ route('deals.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-200 gap-2">
            <i class="fas fa-file-signature"></i>
            <span>إنشاء صفقة جديدة</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('deals.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-end">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 w-full">
                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">حالة الاتفاقية</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">كل الحالات</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة (Draft)</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>بانتظار الاعتماد (Pending Approval)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمدة وموافقة (Approved)</option>
                        <option value="contract_signed" {{ request('status') == 'contract_signed' ? 'selected' : '' }}>عقد موقع (Contract Signed)</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم تسليم السيارة (Delivered)</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة نهائياً (Closed)</option>
                    </select>
                </div>

                <!-- Deal Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">طريقة الشراء والتمويل</label>
                    <select name="deal_type" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">كل الطرق</option>
                        <option value="cash" {{ request('deal_type') == 'cash' ? 'selected' : '' }}>نقداً (Cash)</option>
                        <option value="installment" {{ request('deal_type') == 'installment' ? 'selected' : '' }}>تقسيط داخلي (Installment)</option>
                        <option value="financing" {{ request('deal_type') == 'financing' ? 'selected' : '' }}>تمويل بنكي (Financing)</option>
                        <option value="trade_in" {{ request('deal_type') == 'trade_in' ? 'selected' : '' }}>استبدال (Trade-in)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('deals.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إعادة تعيين
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    تطبيق التصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($deals->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex justify-center items-center w-14 h-14 bg-gray-50 text-gray-400 rounded-full mb-3">
                    <i class="fas fa-handshake text-xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-800">لا يوجد صفقات أو عقود مبيعات مسجلة</h3>
                <p class="text-xs text-gray-500 mt-1">قم بتسجيل صفقتك الأولى للبدء في تتبع التدفق المالي للمبيعات.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-150 text-right">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-555">
                        <tr>
                            <th class="px-6 py-4">رقم الصفقة</th>
                            <th class="px-6 py-4">العميل</th>
                            <th class="px-6 py-4">السيارة المباعة</th>
                            <th class="px-6 py-4">طريقة الشراء</th>
                            <th class="px-6 py-4">السعر الصافي النهائي</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4">مندوب المبيعات</th>
                            <th class="px-6 py-4 text-left">التحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($deals as $deal)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800 font-mono">#{{ $deal->id }}</td>
                                <td class="px-6 py-4 font-bold text-indigo-600">
                                    <a href="{{ route('customers.show', $deal->customer) }}" class="hover:underline">
                                        {{ $deal->customer->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $deal->vehicle->make }} {{ $deal->vehicle->model }} ({{ $deal->vehicle->year }})
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-50 text-gray-700 capitalize">
                                        {{ $deal->deal_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-extrabold text-gray-800 font-mono">
                                    {{ number_format($deal->final_price, 2) }} ر.س
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize 
                                        @if($deal->status === 'draft') bg-gray-100 text-gray-700
                                        @elseif($deal->status === 'pending_approval') bg-amber-50 text-amber-700 border border-amber-100
                                        @elseif($deal->status === 'approved') bg-blue-50 text-blue-700 border border-blue-100
                                        @elseif($deal->status === 'contract_signed') bg-indigo-50 text-indigo-700 border border-indigo-100
                                        @elseif($deal->status === 'delivered') bg-emerald-50 text-emerald-700 border border-emerald-100
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $deal->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $deal->salesperson->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-left">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('deals.show', $deal) }}" class="p-1.5 bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 rounded-lg transition" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('deals.pdf', $deal) }}" class="p-1.5 bg-gray-50 hover:bg-emerald-50 text-gray-600 hover:text-emerald-600 rounded-lg transition" title="تحميل العقد PDF">
                                            <i class="fas fa-file-pdf text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($deals->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $deals->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
