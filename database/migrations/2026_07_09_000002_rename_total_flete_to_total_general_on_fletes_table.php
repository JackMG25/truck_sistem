<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('fletes', 'total_flete') && ! Schema::hasColumn('fletes', 'total_general')) {
            Schema::table('fletes', function (Blueprint $table) {
                $table->renameColumn('total_flete', 'total_general');
            });
        }

        // Total general = suma de items.total (no de items.flete).
        DB::table('fletes')
            ->select('id')
            ->orderBy('id')
            ->each(function ($flete) {
                $totalGeneral = DB::table('flete_items')
                    ->where('flete_id', $flete->id)
                    ->sum('total');

                DB::table('fletes')
                    ->where('id', $flete->id)
                    ->update(['total_general' => $totalGeneral]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('fletes', 'total_general') && ! Schema::hasColumn('fletes', 'total_flete')) {
            Schema::table('fletes', function (Blueprint $table) {
                $table->renameColumn('total_general', 'total_flete');
            });
        }
    }
};
