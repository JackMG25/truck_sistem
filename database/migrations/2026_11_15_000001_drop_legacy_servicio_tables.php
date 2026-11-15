<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('agencias');

        if (! $this->hasIndex('clientes', 'clientes_nombre_index')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index('nombre');
            });
        }

        // Borrar un cliente ya no arrastra sus fletes. El historial se conserva.
        Schema::table('fletes', function (Blueprint $table) {
            $table->dropForeign('fletes_cliente_id_foreign');
            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fletes', function (Blueprint $table) {
            $table->dropForeign('fletes_cliente_id_foreign');
            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes')
                ->cascadeOnDelete();
        });
    }

    private function hasIndex(string $table, string $name): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? null) === $name) {
                return true;
            }
        }

        return false;
    }
};
