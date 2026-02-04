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
        Schema::table('inquiries', function (Blueprint $table) {
            $table->enum('status', ['pending', 'read', 'replied'])->default('pending')->after('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
        });
    }
};
