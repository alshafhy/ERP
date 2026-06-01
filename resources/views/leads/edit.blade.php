@extends('layouts.admin')

@section('title', 'تعديل الفرصة البيعية')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">تعديل الفرصة البيعية</h1>
            <p class="text-xs text-gray-500 mt-1">تحديث تصنيف الفرصة ومواعيد المتابعة للعميل</p>
        </div>
        <a href="{{ route('leads.show', $lead) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للتفاصيل</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('leads.update', $lead) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection (Read-only or select) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">العميل *</label>
                    <select name="customer_id" required class="w-full py-2.5 px-3 rounded-xl border border-gray-250 bg-gray-50 text-sm text-right text-gray-600 focus:outline-none">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $lead->customer_id) == $customer->id ? 'selected' : '' }}>
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
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $lead->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->make }} {{ $vehicle->model }} | {{ $vehicle->color }} | ({{ number_format($vehicle->listing_price) }} ريال)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Source -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">مصدر العميل *</label>
                    <select name="source" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="walk-in" {{ old('source', $lead->source) == 'walk-in' ? 'selected' : '' }}>زيارة للمعرض (Walk-in)</option>
                        <option value="website" {{ old('source', $lead->source) == 'website' ? 'selected' : '' }}>الموقع الإلكتروني (Website)</option>
                        <option value="call" {{ old('source', $lead->source) == 'call' ? 'selected' : '' }}>اتصال هاتفي (Call)</option>
                        <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>توصية عميل (Referral)</option>
                    </select>
                </div>

                <!-- Salesperson Assignment -->
                @if(Auth::user()->hasRole('sales_agent'))
                    <!-- Agents are auto-assigned -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">المسؤول عن المتابعة</label>
                        <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full py-2.5 px-4 rounded-xl border border-gray-100 bg-gray-50 text-sm text-right text-gray-500">
                        <input type="hidden" name="assigned_to" value="{{ $lead->assigned_to }}">
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">تعيين مسؤول مبيعات</label>
                        <select name="assigned_to" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="">-- بدون تعيين --</option>
                            @foreach($salespersons as $salesperson)
                                <option value="{{ $salesperson->id }}" {{ old('assigned_to', $lead->assigned_to) == $salesperson->id ? 'selected' : '' }}>
                                    {{ $salesperson->name }} ({{ $salesperson->roles->pluck('name')->first() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Pipeline Status -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">المرحلة الحالية *</label>
                    <select name="status" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="new" {{ old('status', $lead->status) == 'new' ? 'selected' : '' }}>جديدة (New)</option>
                        <option value="contacted" {{ old('status', $lead->status) == 'contacted' ? 'selected' : '' }}>تم التواصل (Contacted)</option>
                        <option value="qualified" {{ old('status', $lead->status) == 'qualified' ? 'selected' : '' }}>فرصة مؤهلة (Qualified)</option>
                        <option value="lost" {{ old('status', $lead->status) == 'lost' ? 'selected' : '' }}>خاسرة (Lost)</option>
                        <option value="converted" {{ old('status', $lead->status) == 'converted' ? 'selected' : '' }}>ناجحة / مباعة (Converted)</option>
                    </select>
                </div>

                <!-- Follow-up Time -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">موعد المتابعة القادمة</label>
                    <input type="datetime-local" name="follow_up_at" value="{{ old('follow_up_at', $lead->follow_up_at ? $lead->follow_up_at->format('Y-m-d\TH:i') : '') }}" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">متطلبات العميل وتفاصيل الاهتمام</label>
                    <textarea name="notes" rows="3" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('leads.show', $lead) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    تعديل الفرصة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
