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
            if (! Schema::hasColumn('trademark_registrations', 'filing_type')) {
                $table->string('filing_type')->default('madrid')->after('trademark_type');
            }

            if (! Schema::hasColumn('trademark_registrations', 'selected_classes')) {
                $table->json('selected_classes')->nullable()->after('filing_type');
            }

            if (! Schema::hasColumn('trademark_registrations', 'selected_countries')) {
                $table->json('selected_countries')->nullable()->after('selected_classes');
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

            if (Schema::hasColumn('trademark_registrations', 'selected_countries')) {
                $dropColumns[] = 'selected_countries';
            }

            if (Schema::hasColumn('trademark_registrations', 'selected_classes')) {
                $dropColumns[] = 'selected_classes';
            }

            if (Schema::hasColumn('trademark_registrations', 'filing_type')) {
                $dropColumns[] = 'filing_type';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
