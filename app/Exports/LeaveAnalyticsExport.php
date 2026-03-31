<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeaveAnalyticsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee',
            'Department',
            'Leave Type',
            'Start Date',
            'End Date',
            'Duration',
            'Status',
            'Reason',
            'Applied At',
        ];
    }

    public function map($row): array
    {
        return [
            $row['ID'] ?? '',
            $row['Employee'] ?? '',
            $row['Department'] ?? 'N/A',
            $row['Leave Type'] ?? '',
            $row['Start Date'] ?? '',
            $row['End Date'] ?? '',
            $row['Duration'] ?? '',
            $row['Status'] ?? '',
            $row['Reason'] ?? '',
            $row['Applied At'] ?? '',
        ];
    }

    public function title(): string
    {
        return 'Leave Analytics Report';
    }
}
