<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->index('fecha_servicio');
            $table->index('estado_pago');
            $table->index('estado_servicio');
            $table->index('created_at');
            $table->index(['estado_pago', 'fecha_servicio']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->index('fecha_pago');
            $table->index(['servicio_id', 'fecha_pago']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index('nombre');
        });

        Schema::table('agencias', function (Blueprint $table) {
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropIndex(['fecha_servicio']);
            $table->dropIndex(['estado_pago']);
            $table->dropIndex(['estado_servicio']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['estado_pago', 'fecha_servicio']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['fecha_pago']);
            $table->dropIndex(['servicio_id', 'fecha_pago']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['nombre']);
        });

        Schema::table('agencias', function (Blueprint $table) {
            $table->dropIndex(['nombre']);
        });
    }
};
