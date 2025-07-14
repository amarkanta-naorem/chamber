<?php

namespace App\Exports;

use App\Models\Chamber;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChambersExport implements FromCollection, ShouldAutoSize, WithHeadings, WithProperties
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
            'Message'
        ];
    }

    public function properties(): array
    {
        return [
            'creator' => 'Amarkanta Naorem',
            'lastModifiedBy' => 'Amarkanta Naorem',
            'title' => 'Chambers Report',
            'description' => 'Temperature Data Export',
            'company' => 'ITG Telematics',
        ];
    }
}
