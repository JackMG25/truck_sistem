<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('total_flete', 10, 2)->default(0);
            $table->timestamps();

            $table->index('cliente_id');
            $table->index('fecha');
        });

        Schema::create('flete_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flete_id')->constrained('fletes')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('descripcion')->nullable();
            $table->decimal('servicio', 10, 2)->default(0);
            $table->decimal('flete', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();

            $table->index('flete_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flete_items');
        Schema::dropIfExists('fletes');
    }
};
