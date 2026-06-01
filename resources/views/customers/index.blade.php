@extends('layouts.admin')

@section('title', 'إدارة العملاء')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">العملاء (CRM)</h1>
            <p class="text-sm text-gray-500 mt-1">تسجيل وإدارة بيانات العملاء الأفراد والشركات في المعرض</p>
        </div>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-200 gap-2">
            <i class="fas fa-user-plus"></i>
            <span>تسجيل عميل جديد</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('customers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-end">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 w-full">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">بحث عن عميل</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم، رقم الجوال، الهوية الوطنية..." class="w-full pr-10 pl-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">تصنيف العميل</label>
                    <select name="type" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">كل الفئات</option>
                        <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>أفراد (Individual)</option>
                        <option value="corporate" {{ request('type') == 'corporate' ? 'selected' : '' }}>شركات (Corporate)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('customers.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إعادة تعيين
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    تطبيق التصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($customers->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex justify-center items-center w-14 h-14 bg-gray-50 text-gray-400 rounded-full mb-3">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <h3 class="text-base font-bold text-gray-800">لا يوجد عملاء مسجلين</h3>
                <p class="text-xs text-gray-500 mt-1">ابدأ بتسجيل عميلك الأول لتوثيق اتصالاته وفرصه البيعية.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-150 text-right">
                    <thead class="bg-gray-50 text-xs font-bold text-gray-555">
                        <tr>
                            <th class="px-6 py-4">اسم العميل</th>
                            <th class="px-6 py-4">الهوية الوطنية / السجل</th>
                            <th class="px-6 py-4">رقم الجوال</th>
                            <th class="px-6 py-4">البريد الإلكتروني</th>
                            <th class="px-6 py-4">التصنيف</th>
                            <th class="px-6 py-4">تاريخ التسجيل</th>
                            <th class="px-6 py-4 text-left">التحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $customer->name }}</td>
                                <td class="px-6 py-4 font-mono text-gray-600">{{ $customer->national_id ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-mono text-gray-600">{{ $customer->phone }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $customer->email ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $customer->type === 'individual' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $customer->type === 'individual' ? 'فرد' : 'شركة' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">{{ $customer->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-left">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('customers.show', $customer) }}" class="p-1.5 bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 rounded-lg transition" title="عرض التفاصيل">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="p-1.5 bg-gray-50 hover:bg-amber-50 text-gray-600 hover:text-amber-600 rounded-lg transition" title="تعديل">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($customers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $customers->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
