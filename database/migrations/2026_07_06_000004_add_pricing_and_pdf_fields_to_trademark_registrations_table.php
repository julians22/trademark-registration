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
        Schema::table('trademark_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('trademark_registrations', 'class_pricing')) {
                $table->json('class_pricing')->nullable()->after('selected_classes');
            }

            if (! Schema::hasColumn('trademark_registrations', 'total_price')) {
                $table->decimal('total_price', 15, 2)->nullable()->after('class_pricing');
            }

            if (! Schema::hasColumn('trademark_registrations', 'pricing_completed_at')) {
                $table->timestamp('pricing_completed_at')->nullable()->after('total_price');
            }

            if (! Schema::hasColumn('trademark_registrations', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('file_paths');
            }

            if (! Schema::hasColumn('trademark_registrations', 'pdf_generated_at')) {
                $table->timestamp('pdf_generated_at')->nullable()->after('pdf_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trademark_registrations', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('trademark_registrations', 'pdf_generated_at')) {
                $dropColumns[] = 'pdf_generated_at';
            }

            if (Schema::hasColumn('trademark_registrations', 'pdf_path')) {
                $dropColumns[] = 'pdf_path';
            }

            if (Schema::hasColumn('trademark_registrations', 'pricing_completed_at')) {
                $dropColumns[] = 'pricing_completed_at';
            }

            if (Schema::hasColumn('trademark_registrations', 'total_price')) {
                $dropColumns[] = 'total_price';
            }

            if (Schema::hasColumn('trademark_registrations', 'class_pricing')) {
                $dropColumns[] = 'class_pricing';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
