@extends('layouts.admin')

@section('title', 'إنشاء أمر شراء')

@section('content')
<div class="max-w-6xl mx-auto text-right" x-data="poForm()">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-bold text-gray-700">أمر شراء جديد</h3>
        <a href="{{ route('purchase-orders.index') }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
            <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
        </a>
    </div>

    <form action="{{ route('purchase-orders.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="supplier_id">المورد</label>
                    <select name="supplier_id" id="supplier_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-right" required>
                        <option value="">اختر المورد</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} (الرصيد: {{ number_format($supplier->balance, 2) }} ر.س)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">تاريخ الطلب</label>
                    <input type="text" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 text-right" value="{{ date('Y-m-d') }}" readonly>
                </div>
            </div>

            <div class="mb-4 overflow-x-auto">
                <h4 class="text-xl font-bold mb-4 text-gray-700 border-b pb-2">أصناف الطلب</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">المنتج</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase w-32">الكمية</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase w-48">سعر الوحدة</th>
                            <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase w-48">الإجمالي</th>
                            <th class="px-4 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-2">
                                    <select :name="`items[${index}][product_id]`" x-model="item.product_id" class="w-full border rounded p-1 text-right" required>
                                        <option value="">اختر المنتج</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="`items[${index}][qty]`" x-model="item.qty" step="0.01" class="w-full border rounded p-1 text-right" required>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price" step="0.01" class="w-full border rounded p-1 text-right" required>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-gray-700 font-bold" x-text="formatCurrency(item.qty * item.unit_price)"></span>
                                </td>
                                <td class="px-4 py-2 text-left">
                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="mt-4">
                    <button type="button" @click="addItem()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold transition">
                        <i class="fas fa-plus ml-2"></i> إضافة صنف
                    </button>
                </div>
            </div>

            <div class="flex flex-col items-start mt-8 border-t pt-4">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">المجموع الفرعي:</span>
                        <span class="font-bold text-gray-800" x-text="formatCurrency(calculateSubtotal())"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">الضريبة (15%):</span>
                        <span class="font-bold text-gray-800" x-text="formatCurrency(calculateSubtotal() * 0.15)"></span>
                    </div>
                    <div class="flex justify-between text-xl border-t pt-2">
                        <span class="font-bold text-gray-700">الإجمالي النهائي:</span>
                        <span class="font-black text-indigo-700" x-text="formatCurrency(calculateSubtotal() * 1.15)"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-8">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 px-8 rounded-lg shadow-lg focus:outline-none focus:shadow-outline transition duration-150" type="submit">
                    حفظ أمر الشراء
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function poForm() {
        return {
            items: [{ product_id: '', qty: 1, unit_price: 0 }],
            addItem() {
                this.items.push({ product_id: '', qty: 1, unit_price: 0 });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            calculateSubtotal() {
                return this.items.reduce((sum, item) => sum + (item.qty * item.unit_price), 0);
            },
            formatCurrency(value) {
                return parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
            }
        }
    }
</script>
@endsection
