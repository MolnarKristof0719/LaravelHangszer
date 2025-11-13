<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $filePath = database_path('csv/instruments.csv');

        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, ';');
            while (($cols = fgetcsv($handle, 0, ';')) !== false) {
                if (count($header) === count($cols)) {
                    $data[] = array_combine($header, $cols);
                }
            }
            fclose($handle);
        }

        foreach ($data as $row) {
            Instrument::create($row);
        }


    }
}
