@extends('layouts.admin')

@section('title', 'تعديل بيانات السيارة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">تعديل بيانات السيارة</h1>
            <p class="text-xs text-gray-500 mt-1">تحديث معلومات المركبة الفنية أو المالية أو حالة التوفر</p>
        </div>
        <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للتفاصيل</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Core Specifications -->
            <div>
                <h3 class="text-sm font-bold text-indigo-600 mb-4 pb-1 border-b border-gray-100"><i class="fas fa-info-circle ml-2"></i>المعلومات الأساسية والمواصفات الفنية</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- VIN -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">رقم الشاصي (VIN) *</label>
                        <input type="text" name="vin" value="{{ old('vin', $vehicle->vin) }}" required maxlength="17" placeholder="أدخل 17 حرفاً ورقماً" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right uppercase">
                        @error('vin')
                            <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Make -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الشركة المصنعة (الماركة) *</label>
                        <input type="text" name="make" value="{{ old('make', $vehicle->make) }}" required placeholder="مثال: Toyota, Ford" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Model -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الموديل *</label>
                        <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" required placeholder="مثال: Land Cruiser" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">سنة الصنع *</label>
                        <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" required min="1900" max="{{ date('Y') + 1 }}" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Trim -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الفئة / التفاصيل الفنية</label>
                        <input type="text" name="trim" value="{{ old('trim', $vehicle->trim) }}" placeholder="مثال: VXR, M Sport" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">اللون الخارجي *</label>
                        <input type="text" name="color" value="{{ old('color', $vehicle->color) }}" required placeholder="مثال: أبيض لؤلؤي" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Mileage -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الممشى (كم) *</label>
                        <input type="number" name="mileage" value="{{ old('mileage', $vehicle->mileage) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Fuel Type -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">نوع الوقود *</label>
                        <select name="fuel_type" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="Petrol" {{ old('fuel_type', $vehicle->fuel_type) == 'Petrol' ? 'selected' : '' }}>بنزين</option>
                            <option value="Diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'Diesel' ? 'selected' : '' }}>ديزل</option>
                            <option value="Electric" {{ old('fuel_type', $vehicle->fuel_type) == 'Electric' ? 'selected' : '' }}>كهربائي</option>
                            <option value="Hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'Hybrid' ? 'selected' : '' }}>هايبرد</option>
                        </select>
                    </div>

                    <!-- Transmission -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">ناقل الحركة (الجير) *</label>
                        <select name="transmission" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="Automatic" {{ old('transmission', $vehicle->transmission) == 'Automatic' ? 'selected' : '' }}>تلقائي (Automatic)</option>
                            <option value="Manual" {{ old('transmission', $vehicle->transmission) == 'Manual' ? 'selected' : '' }}>عادي (Manual)</option>
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
                        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $vehicle->cost_price) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Listing Price -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">سعر البيع المعروض (SAR) *</label>
                        <input type="number" step="0.01" name="listing_price" value="{{ old('listing_price', $vehicle->listing_price) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                    </div>

                    <!-- Condition -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">حالة الاستخدام *</label>
                        <select name="condition" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="new" {{ old('condition', $vehicle->condition) == 'new' ? 'selected' : '' }}>جديد (New)</option>
                            <option value="used" {{ old('condition', $vehicle->condition) == 'used' ? 'selected' : '' }}>مستعمل (Used)</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">حالة التوفر *</label>
                        <select name="status" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>متاح في المعرض</option>
                            <option value="in_transit" {{ old('status', $vehicle->status) == 'in_transit' ? 'selected' : '' }}>في الطريق (تحت الشحن)</option>
                            <option value="reserved" {{ old('status', $vehicle->status) == 'reserved' ? 'selected' : '' }}>محجوز مؤقتاً</option>
                            <option value="sold" {{ old('status', $vehicle->status) == 'sold' ? 'selected' : '' }}>مباع</option>
                            <option value="damaged" {{ old('status', $vehicle->status) == 'damaged' ? 'selected' : '' }}>متضرر (تحت التجهيز)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Existing Images Section -->
            @if($vehicle->hasMedia('images'))
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-3"><i class="fas fa-images ml-1.5"></i>الصور الحالية</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        @foreach($vehicle->getMedia('images') as $media)
                            <div class="relative group rounded-xl overflow-hidden shadow-sm aspect-video">
                                <img src="{{ $media->getUrl() }}" alt="Vehicle Image" class="object-cover w-full h-full">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Section 3: Add More Images -->
            <div>
                <h3 class="text-sm font-bold text-indigo-600 mb-4 pb-1 border-b border-gray-100"><i class="fas fa-plus-circle ml-2"></i>إضافة صور إضافية</h3>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-indigo-500 transition duration-150">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>رفع المزيد من الصور</span>
                                <input id="file-upload" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG حتى 4MB لكل صورة</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
