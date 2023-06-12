<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class UsersExport extends StringValueBinder implements FromQuery,
    WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function query()
    {
        return User::query();
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->phone,
            $user->email,
            $user->address,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Имя',
            'Телефон',
            'Почта',
            'Адрес',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

        ];
    }


}
