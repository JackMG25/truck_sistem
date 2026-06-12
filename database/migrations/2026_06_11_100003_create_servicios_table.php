<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('agencia_id')->constrained('agencias')->cascadeOnDelete();

            $table->enum('tipo_servicio', ['ENVIO', 'RECOJO']);
            $table->dateTime('fecha_servicio')->nullable();
            $table->integer('cantidad_bultos')->unsigned();
            $table->text('descripcion')->nullable();

            $table->decimal('costo_transporte', 10, 2)->default(0);
            $table->decimal('costo_flete', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('estado_servicio', ['PENDIENTE', 'ENTREGADO'])->default('PENDIENTE');
            $table->enum('estado_pago', ['PENDIENTE', 'PARCIAL', 'PAGADO'])->default('PENDIENTE');

            $table->dateTime('fecha_entrega')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Índices adicionales
            $table->index('cliente_id');
            $table->index('agencia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
