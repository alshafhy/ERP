@extends('layouts.admin')

@section('title', 'إضافة سيارة جديدة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">إضافة سيارة جديدة</h1>
            <p class="text-xs text-gray-500 mt-1">تعبئة بيانات المركبة الفنية والمالية لإضافتها إلى المخازن</p>
        </div>
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للمخزون</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Core Specifications -->
            <div>
                <h3 class="text-sm font-bold text-indigo-600 mb-4 pb-1 border-b border-gray-100"><i class="fas fa-info-circle ml-2"></i>المعلومات الأساسية والمواصفات الفنية</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- VIN (Duplicate detection inside DB / unique check) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">رقم الشاصي (VIN) *</label>
                        <input type="text" name="vin" value="{{ old('vin') }}" required maxlength="17" placeholder="أدخل 17 حرفاً ورقماً" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right uppercase">
                        @error('vin')
                            <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Make -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الشركة المصنعة (الماركة) *</label>
                        <input type="text" name="make" value="{{ old('make') }}" required placeholder="مثال: Toyota, Ford" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Model -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الموديل *</label>
                        <input type="text" name="model" value="{{ old('model') }}" required placeholder="مثال: Land Cruiser" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">سنة الصنع *</label>
                        <input type="number" name="year" value="{{ old('year', date('Y')) }}" required min="1900" max="{{ date('Y') + 1 }}" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Trim -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الفئة / التفاصيل الفنية</label>
                        <input type="text" name="trim" value="{{ old('trim') }}" placeholder="مثال: VXR, M Sport" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">اللون الخارجي *</label>
                        <input type="text" name="color" value="{{ old('color') }}" required placeholder="مثال: أبيض لؤلؤي" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Mileage -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الممشى (كم) *</label>
                        <input type="number" name="mileage" value="{{ old('mileage', 0) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">نوع الوقود *</label>
                        <select name="fuel_type" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="Petrol">بنزين</option>
                            <option value="Diesel">ديزل</option>
                            <option value="Electric">كهربائي</option>
                            <option value="Hybrid">هايبرد</option>
                        </select>
                    </div>

                    <!-- Transmission -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">ناقل الحركة (الجير) *</label>
                        <select name="transmission" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="Automatic">تلقائي (Automatic)</option>
                            <option value="Manual">عادي (Manual)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Financials & Status -->
            <div>
                <h3 class="text-sm font-bold text-indigo-600 mb-4 pb-1 border-b border-gray-100"><i class="fas fa-dollar-sign ml-2"></i>التكلفة والحالة المالية</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Cost Price -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">سعر التكلفة (SAR) *</label>
                        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Listing Price -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">سعر البيع المعروض (SAR) *</label>
                        <input type="number" step="0.01" name="listing_price" value="{{ old('listing_price') }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Condition (New/Used) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">حالة الاستخدام *</label>
                        <select name="condition" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="new">جديد (New)</option>
                            <option value="used">مستعمل (Used)</option>
                        </select>
                    </div>

                    <!-- Inventory Status -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">حالة التوفر الابتدائية *</label>
                        <select name="status" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="available">متاح في المعرض</option>
                            <option value="in_transit">في الطريق (تحت الشحن)</option>
                            <option value="reserved">محجوز مؤقتاً</option>
                            <option value="damaged">متضرر (تحت التجهيز)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Image Showcase Upload (Spatie Media Library) -->
            <div>
                <h3 class="text-sm font-bold text-indigo-600 mb-4 pb-1 border-b border-gray-100"><i class="fas fa-images ml-2"></i>صور السيارة (تصل مباشرة إلى Spatie Media Library)</h3>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-indigo-500 transition duration-150">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>رفع صور متعددة</span>
                                <input id="file-upload" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG حتى 4MB لكل صورة</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('vehicles.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    إضافة السيارة للمخزن
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
