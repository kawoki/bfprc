<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occupied_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained()->cascadeOnDelete();
            $table->morphs('occupiable');
            $table->date('date');
            $table->time('time');
            $table->timestamps();

            $table->index(['table_id', 'date', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occupied_tables');
    }
};
