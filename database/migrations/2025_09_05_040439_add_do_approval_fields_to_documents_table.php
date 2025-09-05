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
            $table->enum('do_approval_status', ['pending', 'accepted', 'rejected'])->default('pending')->after('status');
            $table->string('do_approved_by')->nullable()->after('do_approval_status');
            $table->timestamp('do_approved_at')->nullable()->after('do_approved_by');
            $table->text('do_approval_notes')->nullable()->after('do_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['do_approval_status', 'do_approved_by', 'do_approved_at', 'do_approval_notes']);
        });
    }
};
