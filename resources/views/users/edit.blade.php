@extends('layouts.admin')

@section('title', 'تعديل موظف')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumbs / Back button -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-bold text-gray-500">لوحة التحكم &gt; إدارة الموظفين &gt; تعديل موظف</span>
        <a href="{{ route('users.index') }}" class="text-xs font-extrabold text-indigo-650 hover:underline"><i class="fas fa-arrow-right ml-1"></i> العودة لقائمة الهيكل</a>
    </div>

    <!-- Form card -->
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-right">
        <h2 class="text-lg font-black text-gray-800 border-b border-gray-100 pb-4 mb-6">تعديل بيانات وصلاحيات الموظف</h2>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">اسم الموظف بالكامل *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-right focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="مثال: فهد عبد الله القحطاني">
                </div>

                <!-- Position -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">المسمى الوظيفي *</label>
                    <input type="text" name="position" value="{{ old('position', $user->position) }}" required class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-right focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="مثال: أخصائي تمويل مالي">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">البريد الإلكتروني المهني *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-left focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="employee@emad-cars.com">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">رقم الجوال الشخصي</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-left focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="05xxxxxxxx">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="password" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-left focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="اتركه فارغاً لعدم التغيير">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" name="password_confirmation" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-left focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="اتركه فارغاً لعدم التغيير">
                </div>

                <!-- Branch Allocation -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">فرع العمل المخصص *</label>
                    <select name="branch_id" required class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-right bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        @if(auth()->user()->hasRole('super_admin'))
                            <option value="">كل الفروع (وصول سيادي كامل)</option>
                        @endif
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->city }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-xs font-bold text-gray-650 mb-2">صلاحية النظام الافتراضية *</label>
                    <select name="role" required class="w-full py-2.5 px-4 rounded-xl border border-gray-200 text-xs text-right bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role') ?? ($user->roles->first()?->name)) == $role->name ? 'selected' : '' }}>
                                {{ strtoupper($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                    تحديث بيانات وصلاحيات الموظف
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
