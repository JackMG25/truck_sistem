<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flete_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flete_id')->constrained('fletes')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->timestamps();

            $table->index('flete_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flete_pagos');
    }
};
