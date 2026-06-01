@extends('layouts.admin')

@section('title', 'إنشاء فرصة بيعية جديدة')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">إنشاء فرصة بيعية جديدة (Lead)</h1>
            <p class="text-xs text-gray-500 mt-1">تسجيل اهتمام عميل بمركبة معينة وتخصيص مسؤول متابعة</p>
        </div>
        <a href="{{ route('leads.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للقناة</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('leads.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-bold text-gray-700">حدد العميل *</label>
                        <a href="{{ route('customers.create') }}" class="text-[10px] font-bold text-indigo-600 hover:underline"><i class="fas fa-user-plus ml-0.5"></i>تسجيل عميل جديد أولاً</a>
                    </div>
                    <select name="customer_id" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">-- اختر من العملاء المسجلين --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Vehicle of Interest -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">سيارة الاهتمام</label>
                    <select name="vehicle_id" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">-- حدد السيارة من المعرض (اختياري) --</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->make }} {{ $vehicle->model }} | {{ $vehicle->color }} | ({{ number_format($vehicle->listing_price) }} ريال)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Source -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">مصدر العميل *</label>
                    <select name="source" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="walk-in">زيارة للمعرض (Walk-in)</option>
                        <option value="website">الموقع الإلكتروني (Website)</option>
                        <option value="call">اتصال هاتفي (Call)</option>
                        <option value="referral">توصية عميل (Referral)</option>
                    </select>
                </div>

                <!-- Salesperson Assignment -->
                @if(Auth::user()->hasRole('sales_agent'))
                    <!-- Hidden or pre-selected input because agents can only manage their own leads -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">المسؤول عن المتابعة</label>
                        <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full py-2.5 px-4 rounded-xl border border-gray-100 bg-gray-50 text-sm text-right text-gray-500">
                        <input type="hidden" name="assigned_to" value="{{ Auth::id() }}">
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">تعيين مسؤول مبيعات</label>
                        <select name="assigned_to" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="">-- بدون تعيين حالياً --</option>
                            @foreach($salespersons as $salesperson)
                                <option value="{{ $salesperson->id }}" {{ old('assigned_to', Auth::id()) == $salesperson->id ? 'selected' : '' }}>
                                    {{ $salesperson->name }} ({{ $salesperson->roles->pluck('name')->first() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Initial Pipeline Status -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">المرحلة الحالية *</label>
                    <select name="status" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="new">فرصة جديدة (New)</option>
                        <option value="contacted">تم الاتصال (Contacted)</option>
                        <option value="qualified">فرصة مؤهلة (Qualified)</option>
                    </select>
                </div>

                <!-- Follow-up Time -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">موعد المتابعة القادمة</label>
                    <input type="datetime-local" name="follow_up_at" value="{{ old('follow_up_at') }}" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono">
                </div>

                <!-- Initial notes -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">متطلبات العميل وتفاصيل الاهتمام</label>
                    <textarea name="notes" placeholder="أدخل أية ملاحظات تفصيلية، مثل: الرغبة في لون محدد، الميزانية، طريقة الدفع..." rows="3" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right"></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('leads.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    إنشاء الفرصة البيعية
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
