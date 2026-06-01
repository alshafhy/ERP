@extends('layouts.admin')

@section('title', 'تحويل الفرصة البيعية لصفقة شراء')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">تحويل الفرصة إلى صفقة شراء</h1>
            <p class="text-xs text-gray-500 mt-1">تعبئة البيانات المالية والاتفاق البيعي لإغلاق الفرصة البيعية بنجاح</p>
        </div>
        <a href="{{ route('leads.show', $lead) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للتفاصيل</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-6">
        <!-- Vehicle quick recap -->
        @if($lead->vehicle)
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-400 block mb-1">السيارة المختارة للبيع</span>
                    <span class="text-sm font-extrabold text-gray-800">{{ $lead->vehicle->make }} {{ $lead->vehicle->model }} ({{ $lead->vehicle->year }})</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 block mb-1 text-left">السعر المعروض بالمعرض</span>
                    <span class="text-sm font-black text-indigo-600 font-mono">{{ number_format($lead->vehicle->listing_price, 2) }} ريال</span>
                </div>
            </div>
        @endif

        <form action="{{ route('leads.convert', $lead) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Deal Type -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">طريقة الشراء والتمويل *</label>
                    <select name="deal_type" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="cash">نقداً (Cash)</option>
                        <option value="installment">تقسيط مع المعرض (Installment)</option>
                        <option value="financing">تمويل بنكي (Bank Financing)</option>
                    </select>
                </div>

                <!-- Agreed Price -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">سعر الاتفاق النهائي (SAR) *</label>
                    <input type="number" step="0.01" name="agreed_price" value="{{ old('agreed_price', $lead->vehicle ? $lead->vehicle->listing_price : 0) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono font-bold">
                </div>

                <!-- Discount -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">قيمة الخصم إن وجد (SAR) *</label>
                    <input type="number" step="0.01" name="discount" value="{{ old('discount', 0) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono text-red-600 font-bold">
                </div>

                <!-- Deal Status -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">حالة الصفقة الابتدائية *</label>
                    <select name="status" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="pending">بانتظار سداد العربون / الدفعة الأولى (Pending)</option>
                        <option value="finance_approved">موافقة مبدئية من الجهة التمويلية (Finance Approved)</option>
                        <option value="completed">مكتملة ومسددة (Completed)</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">ملاحظات وشروط الصفقة البيعية</label>
                    <textarea name="notes" placeholder="أدخل أية تفاصيل خاصة بشروط تسليم المركبة، استلام اللوحات، الجهات التمويلية..." rows="3" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right"></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('leads.show', $lead) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    إتمام الصفقة وتسجيل العقد
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
