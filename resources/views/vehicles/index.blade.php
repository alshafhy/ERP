@extends('layouts.admin')

@section('title', 'مخزون السيارات')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">مخزون السيارات</h1>
            <p class="text-sm text-gray-500 mt-1">عرض وإدارة وإضافة المركبات في المعرض</p>
        </div>
        @can('create', App\Models\Vehicle::class)
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-200 gap-2">
                <i class="fas fa-plus"></i>
                <span>إضافة سيارة جديدة</span>
            </a>
        @endcan
    </div>

    <!-- Advanced Filter & Search Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('vehicles.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search term -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">بحث بالاسم أو VIN</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالشركة المصنعة، الموديل، VIN..." class="w-full pr-10 pl-4 py-2 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                    </div>
                </div>

                <!-- Make -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الشركة المصنعة</label>
                    <select name="make" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">كل الشركات</option>
                        @foreach($makes as $make)
                            <option value="{{ $make }}" {{ request('make') == $make ? 'selected' : '' }}>{{ $make }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">حالة التوفر</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">كل الحالات</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>متاحة (Available)</option>
                        <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>محجوزة (Reserved)</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>مباعة (Sold)</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>في الطريق (In Transit)</option>
                        <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>متضررة (Damaged)</option>
                    </select>
                </div>

                <!-- Condition -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">حالة السيارة</label>
                    <select name="condition" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">جديد / مستعمل</option>
                        <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>جديدة</option>
                        <option value="used" {{ request('condition') == 'used' ? 'selected' : '' }}>مستعملة</option>
                    </select>
                </div>

                <!-- Fuel Type -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">نوع الوقود</label>
                    <select name="fuel_type" class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">كل أنواع الوقود</option>
                        <option value="Petrol" {{ request('fuel_type') == 'Petrol' ? 'selected' : '' }}>بنزين</option>
                        <option value="Diesel" {{ request('fuel_type') == 'Diesel' ? 'selected' : '' }}>ديزل</option>
                        <option value="Electric" {{ request('fuel_type') == 'Electric' ? 'selected' : '' }}>كهربائية</option>
                        <option value="Hybrid" {{ request('fuel_type') == 'Hybrid' ? 'selected' : '' }}>هايبرد</option>
                    </select>
                </div>

                <!-- Price Min -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الحد الأدنى للسعر (SAR)</label>
                    <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="من..." class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                </div>

                <!-- Price Max -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">الحد الأقصى للسعر (SAR)</label>
                    <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="إلى..." class="w-full py-2 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('vehicles.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إعادة تعيين
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- Vehicles List/Grid -->
    @if($vehicles->isEmpty())
        <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 text-center">
            <div class="inline-flex justify-center items-center w-16 h-16 bg-gray-50 text-gray-400 rounded-full mb-4">
                <i class="fas fa-car text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">لا توجد سيارات مطابقة</h3>
            <p class="text-sm text-gray-500 mt-1">تأكد من ضبط الفلاتر أو جرب البحث بكلمة أخرى.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($vehicles as $vehicle)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition duration-200 flex flex-col">
                    <!-- Image Showcase -->
                    <div class="relative bg-gray-100 h-48 w-full flex items-center justify-center overflow-hidden">
                        @if($vehicle->hasMedia('images'))
                            <img src="{{ $vehicle->getFirstMediaUrl('images') }}" alt="{{ $vehicle->make }} {{ $vehicle->model }}" class="object-cover w-full h-full">
                        @else
                            <div class="flex flex-col items-center text-gray-400">
                                <i class="fas fa-image text-3xl mb-1"></i>
                                <span class="text-xs">لا تتوفر صور</span>
                            </div>
                        @endif

                        <!-- Stock Aging Pill -->
                        <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-1 rounded-lg backdrop-blur-md {{ $vehicle->days_in_stock > 30 ? 'bg-red-500/90 text-white' : 'bg-black/60 text-white' }}">
                            <i class="far fa-clock ml-1"></i> في المخزن: {{ $vehicle->days_in_stock }} أيام
                        </span>

                        <!-- Condition Pill -->
                        <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-1 rounded-lg {{ $vehicle->condition === 'new' ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                            {{ $vehicle->condition === 'new' ? 'جديدة' : 'مستعملة' }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start gap-2">
                                <h3 class="text-base font-bold text-gray-800 leading-tight">
                                    {{ $vehicle->make }} {{ $vehicle->model }}
                                    <span class="text-xs text-gray-400 block mt-1 font-normal">{{ $vehicle->trim }} | {{ $vehicle->year }}</span>
                                </h3>
                                
                                @if($vehicle->status === 'available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">متاحة</span>
                                @elseif($vehicle->status === 'reserved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">محجوزة</span>
                                @elseif($vehicle->status === 'sold')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200">مباعة</span>
                                @elseif($vehicle->status === 'in_transit')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">في الطريق</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">متضررة</span>
                                @endif
                            </div>

                            <!-- Engine specs -->
                            <div class="grid grid-cols-2 gap-2 mt-4 text-xs text-gray-500 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                <div><i class="fas fa-gas-pump ml-1 text-gray-400"></i>{{ $vehicle->fuel_type }}</div>
                                <div><i class="fas fa-cog ml-1 text-gray-400"></i>{{ $vehicle->transmission }}</div>
                                <div class="col-span-2 mt-1"><i class="fas fa-tachometer-alt ml-1 text-gray-400"></i>ممشى: {{ number_format($vehicle->mileage) }} كم</div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-gray-400 block">سعر البيع المعروض</span>
                                <span class="text-base font-extrabold text-indigo-600">{{ number_format($vehicle->listing_price, 2) }} <span class="text-xs font-normal">ريال</span></span>
                            </div>

                            <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center justify-center p-2.5 bg-gray-50 hover:bg-indigo-50 text-gray-600 hover:text-indigo-600 rounded-xl transition duration-150">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
@endsection
