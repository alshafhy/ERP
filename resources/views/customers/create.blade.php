@extends('layouts.admin')

@section('title', 'تسجيل عميل جديد')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">تسجيل عميل جديد</h1>
            <p class="text-xs text-gray-500 mt-1">إضافة سجل لعميل فرد أو منشأة تجارية في النظام</p>
        </div>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للعملاء</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">الاسم الكامل *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="أدخل اسم العميل ثلاثي أو اسم الشركة" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">رقم الجوال *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="مثال: 0555555555" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono">
                </div>

                <!-- National ID / CR -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">الهوية الوطنية / السجل التجاري</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" placeholder="أدخل 10 خانات" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono">
                    @error('national_id')
                        <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@domain.com" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono">
                </div>

                <!-- Customer Type -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">تصنيف العميل *</label>
                    <select name="type" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="individual">أفراد (Individual)</option>
                        <option value="corporate">شركات / مؤسسات (Corporate)</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">العنوان الجغرافي</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="المدينة، الحي، اسم الشارع" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">ملاحظات / تفاصيل إضافية</label>
                    <textarea name="notes" placeholder="أدخل أية تفاصيل خاصة بالعميل أو طبيعة أعماله..." rows="3" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right"></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    تسجيل العميل
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
