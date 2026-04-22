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
        Schema::create('registration_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registration_id');
            $table->string('word_marks')->nullable();
            $table->string('logo')->nullable();
            $table->text('classifications')->nullable();
            $table->text('goods_services')->nullable();
            $table->string('currency')->nullable();
            $table->string('trademark_administration')->nullable();
            $table->text('countries')->nullable();
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_details');
    }
};
