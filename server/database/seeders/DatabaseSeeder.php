<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //mielott seedelunk minden tablat toroljunk le
        DB::statement('DELETE FROM instruments');

        //Ami Seeder osztaly itt fel van sorolva az lefut
        $this->call([
            InstrumentSeeder::class
        ]);
    }
}
