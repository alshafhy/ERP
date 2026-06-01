@extends('layouts.admin')

@section('title', 'قناة الفرص البيعية CRM')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">قناة المبيعات والفرص (CRM)</h1>
            <p class="text-sm text-gray-500 mt-1">متابعة العملاء المحتملين وتتبع الأداء ومراحل تحويل الصفقات</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('leads.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-200 gap-2">
                <i class="fas fa-plus"></i>
                <span>إنشاء فرصة جديدة</span>
            </a>
        </div>
    </div>

    <!-- Kanban Board Grid -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-start">
        <!-- New Column -->
        @foreach(['new' => ['name' => 'جديدة', 'color' => 'bg-blue-500 text-blue-700 bg-blue-50 border-blue-100'],
                  'contacted' => ['name' => 'تم التواصل', 'color' => 'bg-amber-500 text-amber-700 bg-amber-50 border-amber-100'],
                  'qualified' => ['name' => 'مؤهلة', 'color' => 'bg-indigo-500 text-indigo-700 bg-indigo-50 border-indigo-100'],
                  'lost' => ['name' => 'خاسرة', 'color' => 'bg-red-500 text-red-700 bg-red-50 border-red-100'],
                  'converted' => ['name' => 'ناجحة (مباعة)', 'color' => 'bg-green-500 text-green-700 bg-green-50 border-green-100']] as $statusKey => $statusDetails)
            
            <div class="bg-gray-100/70 p-4 rounded-2xl border border-gray-200/50 flex flex-col h-[75vh]">
                <!-- Column Header -->
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black {{ $statusDetails['color'] }}">
                        {{ $statusDetails['name'] }}
                    </span>
                    <span class="text-xs text-gray-500 font-bold bg-gray-200 px-2 py-0.5 rounded-md">
                        {{ $pipeline[$statusKey]->count() }}
                    </span>
                </div>

                <!-- Column Cards Scroll -->
                <div class="flex-1 overflow-y-auto space-y-3.5 custom-scrollbar pr-1">
                    @if($pipeline[$statusKey]->isEmpty())
                        <div class="text-center py-8 text-gray-400 text-xs">
                            لا يوجد فرص.
                        </div>
                    @else
                        @foreach($pipeline[$statusKey] as $lead)
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between space-y-3.5 relative group">
                                <!-- Overdue indicator -->
                                @if($lead->status !== 'converted' && $lead->status !== 'lost' && $lead->follow_up_at && $lead->follow_up_at->isPast())
                                    <span class="absolute top-2.5 left-2.5 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500" title="متابعة متأخرة!"></span>
                                    </span>
                                @endif

                                <div>
                                    <div class="flex justify-between items-start gap-1">
                                        <a href="{{ route('customers.show', $lead->customer) }}" class="text-xs font-black text-gray-800 hover:text-indigo-600 hover:underline">
                                            {{ $lead->customer->name }}
                                        </a>
                                        <span class="text-[9px] text-gray-400 font-bold uppercase">{{ $lead->source }}</span>
                                    </div>
                                    @if($lead->vehicle)
                                        <div class="text-[10px] font-extrabold text-indigo-600 bg-indigo-50/50 px-2 py-0.5 rounded-md inline-block mt-2">
                                            <i class="fas fa-car ml-1 text-[8px]"></i> {{ $lead->vehicle->make }} {{ $lead->vehicle->model }}
                                        </div>
                                    @endif
                                    <p class="text-[10px] text-gray-500 mt-2 line-clamp-2">{{ $lead->notes }}</p>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 border-t border-gray-50 text-[10px]">
                                    <span class="text-gray-400">
                                        <i class="far fa-user ml-1"></i> {{ $lead->assignedAgent->name ?? 'غير معين' }}
                                    </span>

                                    <a href="{{ route('leads.show', $lead) }}" class="text-indigo-600 font-bold hover:underline">
                                        التفاصيل <i class="fas fa-angle-left mr-0.5"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
