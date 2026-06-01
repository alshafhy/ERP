@extends('layouts.admin')

@section('title', 'تفاصيل العميل ' . $customer->name)

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center w-10 h-10 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl transition duration-150">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $customer->name }}</h1>
                <p class="text-xs text-gray-500 mt-1">تاريخ التسجيل بالمعرض: <span class="font-mono text-gray-700">{{ $customer->created_at->format('Y-m-d') }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-xl transition duration-150 gap-1.5 border border-indigo-150">
                <i class="fas fa-edit"></i>
                <span>تعديل</span>
            </a>
            @can('delete', $customer)
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل نهائياً من النظام؟ لا يمكن التراجع عن هذا الإجراء.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs rounded-xl transition duration-150 gap-1.5 border border-red-150">
                        <i class="fas fa-trash-alt"></i>
                        <span>حذف العميل</span>
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Right Column: Profile details -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4 pb-2 border-b border-gray-50"><i class="fas fa-user-circle ml-2 text-indigo-500"></i>بيانات الهوية والاتصال</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-[10px] text-gray-400 block">الهوية الوطنية / السجل</span>
                        <span class="text-sm font-extrabold text-gray-800 font-mono">{{ $customer->national_id ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 block">رقم الجوال</span>
                        <span class="text-sm font-extrabold text-indigo-600 font-mono">{{ $customer->phone }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 block">البريد الإلكتروني</span>
                        <span class="text-sm font-bold text-gray-800 font-mono">{{ $customer->email ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 block">التصنيف</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $customer->type === 'individual' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }} mt-1">
                            {{ $customer->type === 'individual' ? 'عميل فرد' : 'شركة / منشأة' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 block">العنوان</span>
                        <span class="text-sm text-gray-700">{{ $customer->address ?? 'غير محدد' }}</span>
                    </div>
                    @if($customer->notes)
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <span class="text-[10px] text-gray-400 block mb-1">ملاحظات داخلية</span>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $customer->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Left Column: CRM Leads & Purchased Deals -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Opportunities (CRM Leads) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <div class="flex justify-between items-center pb-2 border-b border-gray-50">
                    <h3 class="text-sm font-bold text-gray-700"><i class="fas fa-filter ml-2 text-indigo-500"></i>الفرص البيعية النشطة (CRM Leads)</h3>
                    <a href="{{ route('leads.create', ['customer_id' => $customer->id]) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800"><i class="fas fa-plus ml-1"></i>إنشاء فرصة جديدة</a>
                </div>

                @if($customer->leads->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-6">لا يوجد فرص بيعية نشطة لهذا العميل.</p>
                @else
                    <div class="space-y-4">
                        @foreach($customer->leads as $lead)
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col md:flex-row justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-800">سيارة الاهتمام:</span>
                                        @if($lead->vehicle)
                                            <a href="{{ route('vehicles.show', $lead->vehicle) }}" class="text-xs font-extrabold text-indigo-600 hover:underline">
                                                {{ $lead->vehicle->make }} {{ $lead->vehicle->model }} ({{ $lead->vehicle->year }})
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">غير محددة</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">{{ $lead->notes }}</p>
                                    @if($lead->follow_up_at)
                                        <span class="text-[10px] text-indigo-500 font-bold block mt-1"><i class="far fa-calendar-alt ml-1"></i>المتابعة القادمة: {{ $lead->follow_up_at->format('Y-m-d H:i') }}</span>
                                    @endif
                                </div>
                                <div class="flex md:flex-col justify-between items-end gap-2 text-left">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold capitalize {{ $lead->status === 'converted' ? 'bg-green-50 text-green-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $lead->status }}
                                    </span>
                                    <a href="{{ route('leads.show', $lead) }}" class="text-xs font-bold text-gray-500 hover:text-indigo-600">إدارة الفرصة <i class="fas fa-angle-left mr-1"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Purchased Deals -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 pb-2 border-b border-gray-50"><i class="fas fa-handshake ml-2 text-indigo-500"></i>الصفقات وعقود الشراء (Purchased Deals)</h3>
                
                @if($customer->deals->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-6">لم يقم العميل بإجراء صفقات شراء كاملة حتى الآن.</p>
                @else
                    <div class="space-y-4">
                        @foreach($customer->deals as $deal)
                            <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100 flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-bold text-emerald-900">
                                        شراء: {{ $deal->vehicle->make ?? 'N/A' }} {{ $deal->vehicle->model ?? 'N/A' }}
                                    </h4>
                                    <span class="text-[10px] text-emerald-700 block mt-1">طريقة الشراء: <span class="font-bold uppercase">{{ $deal->deal_type }}</span></span>
                                </div>
                                <div class="text-left">
                                    <span class="text-xs font-extrabold text-emerald-800 block">{{ number_format($deal->agreed_price, 2) }} ريال</span>
                                    <span class="text-[9px] text-gray-400 font-bold block mt-1">الحالة: {{ $deal->status }}</span>
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
