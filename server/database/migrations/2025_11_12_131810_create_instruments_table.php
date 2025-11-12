<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('instruments', function (Blueprint $table) {
        $table->integer('id')->autoIncrement();
        $table->primary('id');
        $table->string('name', 255)->nullable()->default(null); // Ha indexelni akarjuk, ne legyen nagyobb méretű régi mysql motor esetén (mysql: 5.7.7 alatt)
        $table->text('description')->nullable()->default(null);
        $table->string('brand', 255)->nullable()->default(null);
        $table->Integer('price')->nullable()->default(null);
        $table->Integer('quantity')->nullable()->default(null);

       
       
    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruments');
    }
};
