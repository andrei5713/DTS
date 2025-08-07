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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->string('document_type');
            $table->text('subject');
            $table->date('document_date')->nullable();
            $table->date('entry_date');
            $table->string('upload_by'); // User name who uploaded
            $table->unsignedBigInteger('upload_by_user_id'); // User ID who uploaded
            $table->string('upload_to'); // User name who should receive
            $table->unsignedBigInteger('upload_to_user_id'); // User ID who should receive
            $table->string('originating_office'); // Department/Division of uploader
            $table->string('forward_to_department')->nullable(); // Department to forward to
            $table->string('origin_type')->default('internal');
            $table->string('priority');
            $table->text('remarks')->nullable();
            $table->string('routing')->default('internal');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->enum('status', ['pending', 'received', 'forwarded', 'rejected', 'archived'])->default('pending');
            $table->unsignedBigInteger('current_recipient_id')->nullable(); // Current user who should receive
            $table->text('forward_notes')->nullable(); // Notes when forwarding
            $table->timestamps();
            
            $table->foreign('upload_by_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('upload_to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('current_recipient_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
