<?php

namespace App\Exports;

use App\Models\Deal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $agentId;
    protected $branchId;

    public function __construct($startDate = null, $endDate = null, $agentId = null, $branchId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->agentId = $agentId;
        $this->branchId = $branchId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Deal::with(['customer', 'vehicle', 'salesperson', 'branch'])
            ->whereIn('status', ['delivered', 'closed']);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        if ($this->agentId) {
            $query->where('salesperson_id', $this->agentId);
        }
        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        return $query->get();
    }

    /**
     * Define sheet headers
     */
    public function headings(): array
    {
        return [
            'رقم الصفقة',
            'العميل',
            'المركبة المباعة',
            'رقم الهيكل (VIN)',
            'النوع',
            'سعر الاتفاق',
            'الخصم الممنوح',
            'تنزيل المقايضة',
            'الصافي المطلوب',
            'المسؤول المالي',
            'الفرع',
            'الحالة',
            'تاريخ الإنشاء',
        ];
    }

    /**
     * Map model data to columns
     */
    public function map($deal): array
    {
        return [
            $deal->id,
            $deal->customer->name,
            $deal->vehicle->make . ' ' . $deal->vehicle->model . ' (' . $deal->vehicle->year . ')',
            $deal->vehicle->vin,
            strtoupper($deal->deal_type),
            $deal->agreed_price . ' ر.س',
            $deal->discount . ' ر.س',
            $deal->trade_in_value . ' ر.س',
            $deal->final_price . ' ر.س',
            $deal->salesperson->name,
            $deal->branch ? $deal->branch->name : 'إدارة سيادية',
            strtoupper($deal->status),
            $deal->created_at->format('Y-m-d H:i'),
        ];
    }
}
