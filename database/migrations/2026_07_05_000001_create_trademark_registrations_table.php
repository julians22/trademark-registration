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
        Schema::create('trademark_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_type');
            $table->string('applicant_name');
            $table->string('applicant_company')->nullable();
            $table->string('applicant_email');
            $table->string('active_phone_number');
            $table->string('whatsapp_number')->nullable();
            $table->string('wechat_number')->nullable();
            $table->string('trademark_name');
            $table->string('trademark_type');
            $table->string('filing_type')->default('madrid');
            $table->json('selected_classes')->nullable();
            $table->json('selected_countries')->nullable();
            $table->json('file_paths')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trademark_registrations');
    }
};
