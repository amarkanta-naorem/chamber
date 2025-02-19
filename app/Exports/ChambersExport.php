<?php

namespace App\Exports;

use App\Models\Chamber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChambersExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    protected $formattedChambers;

    public function __construct(array $formattedChambers)
    {
        $this->formattedChambers = $formattedChambers;
    }

    public function collection()
    {
        return collect($this->formattedChambers);
    }

    public function headings(): array
    {
        return [
            'System Service ID',
            'Reporting Date',
            'Reporting Time',
            'GPS Time',
            'Temperature',
            'Reporting Date',
            'Reporting Time',
            'GPS Time',
            'Temperature',
        ];
    }
}
