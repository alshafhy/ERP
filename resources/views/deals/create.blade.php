@extends('layouts.admin')

@section('title', 'إنشاء صفقة بيع جديدة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-xl font-bold text-gray-800">إنشاء صفقة بيع جديدة</h1>
            <p class="text-xs text-gray-500 mt-1">تحديد المشتري، السيارة المباعة، والقيمة المالية المتفق عليها</p>
        </div>
        <a href="{{ route('deals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition duration-150 gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للصفقات</span>
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('deals.store') }}" method="POST" class="space-y-6" id="deal-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">حدد المشتري *</label>
                    <select name="customer_id" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">-- اختر من العملاء --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Vehicle Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">حدد السيارة المباعة *</label>
                    <select name="vehicle_id" id="vehicle_id" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="">-- اختر السيارة من المعرض --</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" data-price="{{ $vehicle->listing_price }}" {{ old('vehicle_id', $selectedVehicleId) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->make }} {{ $vehicle->model }} | {{ $vehicle->color }} | ({{ number_format($vehicle->listing_price) }} ر.س)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Deal Type Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">طريقة الشراء والتمويل *</label>
                    <select name="deal_type" id="deal_type" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="cash" {{ old('deal_type') == 'cash' ? 'selected' : '' }}>نقداً (Cash)</option>
                        <option value="installment" {{ old('deal_type') == 'installment' ? 'selected' : '' }}>تقسيط داخلي (Installment)</option>
                        <option value="financing" {{ old('deal_type') == 'financing' ? 'selected' : '' }}>تمويل بنكي (Bank Financing)</option>
                        <option value="trade_in" {{ old('deal_type') == 'trade_in' ? 'selected' : '' }}>استبدال وتجارة سيارة (Trade-in)</option>
                    </select>
                </div>

                <!-- Salesperson Assignment -->
                @if(Auth::user()->hasRole('sales_agent'))
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">مندوب المبيعات</label>
                        <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full py-2.5 px-4 rounded-xl border border-gray-100 bg-gray-50 text-sm text-right text-gray-500">
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">تعيين مندوب مبيعات</label>
                        <select name="salesperson_id" class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                            @foreach($salespersons as $salesperson)
                                <option value="{{ $salesperson->id }}" {{ old('salesperson_id', Auth::id()) == $salesperson->id ? 'selected' : '' }}>
                                    {{ $salesperson->name }} ({{ $salesperson->roles->pluck('name')->first() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Initial Deal status -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">حالة الاتفاقية الأولى *</label>
                    <select name="status" required class="w-full py-2.5 px-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right">
                        <option value="draft">مسودة اتفاقية (Draft)</option>
                        <option value="pending_approval">إرسال لطلب الاعتماد المالي (Pending Approval)</option>
                    </select>
                </div>

                <!-- Pricing Math Block -->
                <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 md:col-span-2 space-y-4">
                    <h3 class="text-xs font-bold text-indigo-900 pb-2 border-b border-indigo-100"><i class="fas fa-calculator ml-2"></i>الحسابات المالية للصفقة</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Agreed Price -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">سعر الاتفاق النهائي *</label>
                            <input type="number" step="0.01" name="agreed_price" id="agreed_price" value="{{ old('agreed_price', 0) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono font-bold">
                        </div>

                        <!-- Discount -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">قيمة الخصم *</label>
                            <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', 0) }}" required min="0" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right font-mono font-bold text-red-600">
                        </div>

                        <!-- Final Calc preview -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">الصافي الإجمالي المستحق</label>
                            <div class="w-full py-2.5 px-4 rounded-xl bg-indigo-900 text-white font-mono font-bold text-sm text-center" id="final_price_preview">
                                0.00 ر.س
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trade-in fields Panel (JS toggled) -->
                <div class="bg-amber-50/40 p-6 rounded-2xl border border-amber-100 md:col-span-2 space-y-4 hidden" id="trade_in_panel">
                    <h3 class="text-xs font-bold text-amber-900 pb-2 border-b border-amber-100"><i class="fas fa-exchange-alt ml-2"></i>بيانات السيارة المستبدلة (Trade-in Vehicle)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-amber-900 mb-1.5">الشركة المصنعة للسيارة المستبدلة *</label>
                            <input type="text" name="trade_in_make" value="{{ old('trade_in_make') }}" placeholder="مثال: تويوتا" class="w-full py-2.5 px-4 rounded-xl border border-amber-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm text-right">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-amber-900 mb-1.5">موديل السيارة المستبدلة *</label>
                            <input type="text" name="trade_in_model" value="{{ old('trade_in_model') }}" placeholder="مثال: كامري" class="w-full py-2.5 px-4 rounded-xl border border-amber-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm text-right">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-amber-900 mb-1.5">سنة الصنع *</label>
                            <input type="number" name="trade_in_year" value="{{ old('trade_in_year') }}" placeholder="مثال: 2018" class="w-full py-2.5 px-4 rounded-xl border border-amber-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm text-right font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-amber-900 mb-1.5">الرقم التسلسلي للسيارة (VIN) *</label>
                            <input type="text" name="trade_in_vin" value="{{ old('trade_in_vin') }}" placeholder="أدخل 17 خانة" class="w-full py-2.5 px-4 rounded-xl border border-amber-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm text-right font-mono uppercase">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-amber-900 mb-1.5">القيمة المقدرة للسيارة المستبدلة (ستخصم من العقد) *</label>
                            <input type="number" step="0.01" name="trade_in_value" id="trade_in_value" value="{{ old('trade_in_value', 0) }}" min="0" class="w-full py-2.5 px-4 rounded-xl border border-amber-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-sm text-right font-mono font-bold text-amber-900">
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">شروط خاصة / ملاحظات</label>
                    <textarea name="notes" placeholder="أدخل تفاصيل التمويل، الدفعة الأولى، البنك الممول، أو تفاصيل التسليم..." rows="3" class="w-full py-2.5 px-4 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm text-right"></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('deals.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-xl transition duration-150">
                    إلغاء
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition duration-150">
                    إنشاء وحفظ الصفقة
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dealType = document.getElementById('deal_type');
        const vehicleSelect = document.getElementById('vehicle_id');
        const agreedInput = document.getElementById('agreed_price');
        const discountInput = document.getElementById('discount');
        const tradeInPanel = document.getElementById('trade_in_panel');
        const tradeInValueInput = document.getElementById('trade_in_value');
        const finalPreview = document.getElementById('final_price_preview');

        // Auto pre-populate agreed price on vehicle selection
        vehicleSelect.addEventListener('change', function () {
            const selectedOpt = vehicleSelect.options[vehicleSelect.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.price) {
                agreedInput.value = parseFloat(selectedOpt.dataset.price);
                calculateFinal();
            }
        });

        // Toggle Trade-in panel
        dealType.addEventListener('change', function () {
            if (dealType.value === 'trade_in') {
                tradeInPanel.classList.remove('hidden');
            } else {
                tradeInPanel.classList.add('hidden');
                tradeInValueInput.value = 0;
            }
            calculateFinal();
        });

        // Trigger on load
        if (dealType.value === 'trade_in') {
            tradeInPanel.classList.remove('hidden');
        }

        // Live calculation logic
        function calculateFinal() {
            const agreed = parseFloat(agreedInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const tradeVal = dealType.value === 'trade_in' ? (parseFloat(tradeInValueInput.value) || 0) : 0;
            
            const finalPrice = Math.max(0, agreed - discount - tradeVal);
            finalPreview.textContent = finalPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ر.س';
        }

        agreedInput.addEventListener('input', calculateFinal);
        discountInput.addEventListener('input', calculateFinal);
        tradeInValueInput.addEventListener('input', calculateFinal);

        // Pre-run on load
        const initialSelected = vehicleSelect.options[vehicleSelect.selectedIndex];
        if (initialSelected && initialSelected.dataset.price && parseFloat(agreedInput.value) === 0) {
            agreedInput.value = parseFloat(initialSelected.dataset.price);
        }
        calculateFinal();
    });
</script>
@endsection
