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
            $table->unsignedBigInteger('forwarded_by_user_id')->nullable()->after('upload_to_user_id');
            $table->string('forwarded_by')->nullable()->after('forwarded_by_user_id');
            $table->foreign('forwarded_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['forwarded_by_user_id']);
            $table->dropColumn(['forwarded_by_user_id', 'forwarded_by']);
        });
    }
};
