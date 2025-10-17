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
        Schema::table('documents', function (Blueprint $table) {
            // Modify the status enum to include 'complied'
            $table->enum('status', ['pending', 'received', 'forwarded', 'rejected', 'archived', 'complied'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('status', ['pending', 'received', 'forwarded', 'rejected', 'archived'])->default('pending')->change();
        });
    }
};
