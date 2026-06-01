@extends('layouts.admin')

@section('title', 'إدارة الفرصة البيعية لـ ' . $lead->customer->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('leads.index') }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl transition duration-150">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">تفاصيل الفرصة البيعية (Lead Sheet)</h1>
                <p class="text-xs text-gray-500 mt-1">حالة المتابعة الحالية: 
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase bg-indigo-50 text-indigo-700 mr-2">
                        {{ $lead->status }}
                    </span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($lead->status !== 'converted' && $lead->status !== 'lost' && $lead->vehicle_id)
                <a href="{{ route('leads.convert.form', $lead) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow-sm transition duration-150 gap-1.5">
                    <i class="fas fa-check-circle"></i>
                    <span>تحويل إلى صفقة ناجحة 🎉</span>
                </a>
            @endif

            <a href="{{ route('leads.edit', $lead) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl transition duration-150 gap-1.5 border border-indigo-150">
                <i class="fas fa-edit"></i>
                <span>تعديل البيانات</span>
            </a>

            @can('delete', $lead)
                <form action="{{ route('leads.destroy', $lead) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الفرصة البيعية نهائياً؟');">
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

    <!-- Overdue Follow-up Reminder Banner -->
    @if($lead->status !== 'converted' && $lead->status !== 'lost' && $lead->follow_up_at && $lead->follow_up_at->isPast())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl flex items-center justify-between animate-pulse">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold">تنبيه: موعد المتابعة متأخر جداً!</h4>
                    <p class="text-[10px] text-red-600 mt-0.5">كان من المفترض إجراء اتصال متابعة في: {{ $lead->follow_up_at->format('Y-m-d H:i') }} (منذ {{ $lead->follow_up_at->diffForHumans() }})</p>
                </div>
            </div>
            <span class="text-[10px] font-bold bg-red-100 px-2.5 py-1 rounded-lg">متأخر (Overdue)</span>
        </div>
    @endif

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Right Column: Profile Cards -->
        <div class="space-y-6">
            <!-- Customer Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 mb-4 pb-2 border-b border-gray-50"><i class="fas fa-user-tag ml-2 text-indigo-500"></i>بيانات العميل المهتم</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-400 text-xs">الاسم:</span>
                        <a href="{{ route('customers.show', $lead->customer) }}" class="font-extrabold text-indigo-600 hover:underline">{{ $lead->customer->name }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 text-xs">رقم الجوال:</span>
                        <span class="font-bold text-gray-800 font-mono">{{ $lead->customer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 text-xs">مصدر الفرصة:</span>
                        <span class="font-bold text-gray-700 capitalize">{{ $lead->source }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400 text-xs">مسؤول المتابعة:</span>
                        <span class="font-bold text-gray-700">{{ $lead->assignedAgent->name ?? 'غير معين' }}</span>
                    </div>
                </div>
            </div>

            <!-- Vehicle of Interest Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 mb-4 pb-2 border-b border-gray-50"><i class="fas fa-car ml-2 text-indigo-500"></i>السيارة المرغوبة</h3>
                @if($lead->vehicle)
                    <div class="space-y-3">
                        <div class="aspect-video bg-gray-50 rounded-xl overflow-hidden shadow-sm relative">
                            @if($lead->vehicle->hasMedia('images'))
                                <img src="{{ $lead->vehicle->getFirstMediaUrl('images') }}" alt="Car" class="object-cover w-full h-full">
                            @else
                                <div class="flex flex-col items-center justify-center h-full text-gray-400 text-xs">
                                    <i class="fas fa-car text-3xl mb-1"></i>
                                    <span>لا تتوفر صور</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <div>
                                <a href="{{ route('vehicles.show', $lead->vehicle) }}" class="text-xs font-black text-indigo-600 hover:underline">
                                    {{ $lead->vehicle->make }} {{ $lead->vehicle->model }}
                                </a>
                                <span class="text-[10px] text-gray-400 block mt-0.5">{{ $lead->vehicle->year }} | {{ $lead->vehicle->transmission }}</span>
                            </div>
                            <span class="text-sm font-black text-indigo-900">{{ number_format($lead->vehicle->listing_price, 2) }} ريال</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-400 text-center py-4">لم يتم ربط أية سيارة اهتمام بعد.</p>
                @endif
            </div>
        </div>

        <!-- Left Column: Activity Log form & Activity log timeline -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Form to log new activity -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-plus-circle ml-2 text-indigo-500"></i>تسجيل اتصال ومتابعة جديدة</h3>
                
                <form action="{{ route('leads.activity', $lead) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex gap-4">
                        <label class="inline-flex items-center text-xs text-gray-700">
                            <input type="radio" name="type" value="call" required checked class="form-radio text-indigo-600 ml-1.5">
                            <span><i class="fas fa-phone-alt text-gray-400 ml-1"></i>اتصال هاتفي</span>
                        </label>
                        <label class="inline-flex items-center text-xs text-gray-700">
                            <input type="radio" name="type" value="message" required class="form-radio text-indigo-600 ml-1.5">
                            <span><i class="fab fa-whatsapp text-gray-400 ml-1"></i>رسالة WhatsApp</span>
                        </label>
                        <label class="inline-flex items-center text-xs text-gray-700">
                            <input type="radio" name="type" value="meeting" required class="form-radio text-indigo-600 ml-1.5">
                            <span><i class="fas fa-handshake text-gray-400 ml-1"></i>مقابلة بالمعرض</span>
                        </label>
                        <label class="inline-flex items-center text-xs text-gray-700">
                            <input type="radio" name="type" value="note" required class="form-radio text-indigo-600 ml-1.5">
                            <span><i class="fas fa-sticky-note text-gray-400 ml-1"></i>ملاحظة عامة</span>
                        </label>
                    </div>

                    <div>
                        <textarea name="description" required placeholder="أدخل تفاصيل التواصل، ما تم مناقشته، استجابة العميل..." rows="2" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-xs text-right"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition duration-150">
                            تسجيل وحفظ النشاط
                        </button>
                    </div>
                </form>
            </div>

            <!-- Timeline -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-clipboard-list ml-2 text-indigo-500"></i>سجل الاتصالات والمتابعات والأنشطة</h3>
                
                @if($lead->activities->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-6">لم يتم تسجيل أية أنشطة تفاعلية حتى الآن.</p>
                @else
                    <div class="relative border-r-2 border-gray-100 pr-4 space-y-6 mr-2">
                        @foreach($lead->activities as $activity)
                            <div class="relative">
                                <!-- Icon badge selector -->
                                <span class="absolute -right-6 top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-white border-2 border-indigo-500 shadow-sm text-[8px] text-indigo-600">
                                    @if($activity->type === 'call') <i class="fas fa-phone-alt"></i>
                                    @elseif($activity->type === 'message') <i class="fab fa-whatsapp"></i>
                                    @elseif($activity->type === 'meeting') <i class="fas fa-handshake"></i>
                                    @else <i class="fas fa-sticky-note"></i> @endif
                                </span>

                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md capitalize">
                                            {{ $activity->type }}
                                        </span>
                                        <span class="text-gray-400 font-mono">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-700 mt-2.5 leading-relaxed">{{ $activity->description }}</p>
                                    <span class="text-[9px] text-gray-400 block mt-2 text-left">بواسطة: {{ $activity->creator->name ?? 'نظام CRM' }}</span>
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
