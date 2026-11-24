<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dealer_id')->constrained()->cascadeOnDelete();
            $table->string('model');
            $table->string('size')->index();
            $table->string('color')->index();
            $table->string('fuel')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
