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
use poster\src\PosterApi;
use Illuminate\Support\Collection;


class PosterUsersExport extends StringValueBinder implements FromCollection,
    WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function collection()
    {
        // Setting up account and token for requests
        PosterApi::init([
            'account_name' => config('poster.account_name'),
            'access_token' => config('poster.access_token'),
        ]);
        // Reading settings
        $data = PosterApi::clients()->getClients();

        return new Collection($data->response);
    }

    public function map($user): array
    {
        return [
            $user->client_id,
            $user->firstname . " " . $user->lastname,
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
