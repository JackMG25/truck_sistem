<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->dateTime('fecha_pago');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['EFECTIVO', 'YAPE', 'PLIN', 'TRANSFERENCIA']);
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index('servicio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
