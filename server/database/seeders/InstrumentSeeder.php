<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        $filePath = database_path('csv/products.csv');
        $data = [];
        $header = []; // Fejlécek tárolására
 
        if (($handle = fopen($filePath, 'r')) !== false) {
            // 1. Beolvassuk a fejléceket (ha vannak)
            $header = fgetcsv($handle, 0, ';');
 
            // 2. Soronként beolvassuk az adatokat (0 azt jelenti, hogy nincs korlát a beolvasott sorra)
            while (($cols = fgetcsv($handle, 0, ';')) !== false) {
                if (count($header) === count($cols)) {
                    // Asszociatív tömb létrehozása (jobb olvashatóság!)
                    $data[] = array_combine($header, $cols);
                }
            }
            // 3. Zárjuk a fájlt (itt kötelező!)
            fclose($handle);
        }

        if (Product::count() === 0) {
            Product::factory()->createMany($data);
        }
    }
}
