@extends('layouts.admin')

@section('title', $vehicle->make . ' ' . $vehicle->model)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl transition duration-150">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    {{ $vehicle->make }} {{ $vehicle->model }}
                    <span class="text-xs font-semibold text-gray-400 mr-2">{{ $vehicle->trim }}</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1">رمز الشاصي الفريد: <span class="font-mono text-gray-700 font-bold uppercase">{{ $vehicle->vin }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            @can('update', $vehicle)
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl transition duration-150 gap-1.5 border border-indigo-150">
                    <i class="fas fa-edit"></i>
                    <span>تعديل</span>
                </a>
            @endcan

            @can('delete', $vehicle)
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه المركبة نهائياً من المخزون؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs rounded-xl transition duration-150 gap-1.5 border border-red-150">
                        <i class="fas fa-trash-alt"></i>
                        <span>حذف</span>
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Right Column: Gallery & Technical Specifications -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Showcase Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-camera ml-2 text-indigo-500"></i>معرض صور السيارة</h3>
                
                @if($vehicle->hasMedia('images'))
                    <!-- Main Image & Thumbnail Grid -->
                    <div class="space-y-4">
                        <div class="aspect-video bg-gray-100 rounded-2xl overflow-hidden shadow-sm relative">
                            <img src="{{ $vehicle->getFirstMediaUrl('images') }}" alt="Main Image" class="object-cover w-full h-full">
                        </div>
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($vehicle->getMedia('images') as $index => $media)
                                <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden shadow-sm hover:ring-2 hover:ring-indigo-500 transition duration-150 cursor-pointer">
                                    <img src="{{ $media->getUrl() }}" alt="Thumbnail {{ $index + 1 }}" class="object-cover w-full h-full">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="aspect-video bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-images text-4xl mb-2"></i>
                        <p class="text-xs">لم يتم رفع أي صور لهذه السيارة حتى الآن</p>
                    </div>
                @endif
            </div>

            <!-- Detailed Specifications Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-sliders-h ml-2 text-indigo-500"></i>المواصفات الفنية التفصيلية</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">الشركة المصنعة</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->make }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">الموديل الفني</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->model }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">سنة الصنع</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->year }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">الفئة / التجهيز</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->trim ?? 'N/A' }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">الممشى الحالي</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ number_format($vehicle->mileage) }} كم</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">نوع الوقود</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->fuel_type }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">ناقل الحركة</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->transmission }}</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 block mb-1">اللون الخارجي</span>
                        <span class="text-sm font-extrabold text-gray-800">{{ $vehicle->color }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column: Status Management & Financials -->
        <div class="space-y-6">
            <!-- Stock Status & Stock Aging Indicator Banner -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-box-open ml-2 text-indigo-500"></i>حالة المخزون والتخزين</h3>
                
                <!-- Stock Aging indicator with visual metrics -->
                <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-indigo-500 block">مدة بقاء السيارة بالمخزن</span>
                        <span class="text-lg font-black text-indigo-900 mt-0.5">{{ $vehicle->days_in_stock }} يوماً</span>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="far fa-calendar-alt text-xl"></i>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-center">
                        <span class="text-[9px] text-gray-400 block">حالة التوفر</span>
                        <span class="text-xs font-bold text-gray-800 uppercase block mt-1">
                            @if($vehicle->status === 'available') متاحة
                            @elseif($vehicle->status === 'reserved') محجوزة
                            @elseif($vehicle->status === 'sold') مباعة
                            @elseif($vehicle->status === 'in_transit') في الطريق
                            @else متضررة @endif
                        </span>
                    </div>
                    <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-center">
                        <span class="text-[9px] text-gray-400 block">حالة الاستخدام</span>
                        <span class="text-xs font-bold text-gray-800 block mt-1">{{ $vehicle->condition === 'new' ? 'جديدة (0 كم)' : 'مستعملة' }}</span>
                    </div>
                </div>
            </div>

            <!-- Cost & Financial Analysis Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-wallet ml-2 text-indigo-500"></i>التقرير المالي والتسعير</h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100 text-sm">
                        <span class="text-gray-500">سعر التكلفة الأصلي:</span>
                        <span class="font-bold text-gray-850">{{ number_format($vehicle->cost_price, 2) }} ريال</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100 text-sm">
                        <span class="text-gray-500">السعر المعروض للبيع:</span>
                        <span class="font-black text-indigo-600">{{ number_format($vehicle->listing_price, 2) }} ريال</span>
                    </div>
                    @php
                        $margin = $vehicle->listing_price - $vehicle->cost_price;
                        $marginPercentage = $vehicle->cost_price > 0 ? ($margin / $vehicle->cost_price) * 100 : 0;
                    @endphp
                    <div class="flex justify-between py-2.5 bg-emerald-50 rounded-xl px-3 border border-emerald-100 text-sm">
                        <span class="text-emerald-800 font-semibold">هامش الربح المتوقع:</span>
                        <span class="font-black text-emerald-800">{{ number_format($margin, 2) }} ريال ({{ number_format($marginPercentage, 1) }}%)</span>
                    </div>
                </div>
            </div>

            <!-- Status Transition Control (available -> reserved -> sold) -->
            @can('updateStatus', $vehicle)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-random ml-2 text-indigo-500"></i>تحديث حالة التوفر</h3>
                    <form action="{{ route('vehicles.update-status', $vehicle) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">حدد الحالة الجديدة</label>
                            <select name="status" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-xs text-right">
                                <option value="available" {{ $vehicle->status === 'available' ? 'selected' : '' }}>متاح في المعرض (Available)</option>
                                <option value="reserved" {{ $vehicle->status === 'reserved' ? 'selected' : '' }}>محجوز مؤقتاً (Reserved)</option>
                                <option value="sold" {{ $vehicle->status === 'sold' ? 'selected' : '' }}>مباع (Sold)</option>
                                <option value="in_transit" {{ $vehicle->status === 'in_transit' ? 'selected' : '' }}>في الطريق (In Transit)</option>
                                <option value="damaged" {{ $vehicle->status === 'damaged' ? 'selected' : '' }}>متضرر / تحت الصيانة (Damaged)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1">ملاحظات التغيير</label>
                            <textarea name="notes" placeholder="أدخل أسباب تغيير حالة توفر السيارة (مثل: دفع عربون، بيع نقدي...)" rows="2" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-xs text-right"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150">
                            تحديث وتوثيق الحالة
                        </button>
                    </form>
                </div>
            @endcan

            <!-- Status Change History Logs (available -> reserved -> sold) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-history ml-2 text-indigo-500"></i>سجل الحالات والمتابعة</h3>
                
                @if($vehicle->statusLogs->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">لا يوجد سجل تغييرات موثق.</p>
                @else
                    <div class="relative border-r-2 border-gray-100 pr-4 space-y-4 mr-2">
                        @foreach($vehicle->statusLogs as $log)
                            <div class="relative">
                                <!-- Status Marker -->
                                <span class="absolute -right-6 top-1.5 flex h-3 w-3 items-center justify-center rounded-full bg-indigo-600 ring-4 ring-white"></span>
                                
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                            {{ $log->status_to }}
                                        </span>
                                        <span class="text-[9px] text-gray-400">{{ $log->changed_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    @if($log->notes)
                                        <p class="text-xs text-gray-600 mt-2 leading-relaxed">{{ $log->notes }}</p>
                                    @endif
                                    <span class="text-[9px] text-gray-400 block mt-2">بواسطة: {{ $log->user->name ?? 'نظام المعرض' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
