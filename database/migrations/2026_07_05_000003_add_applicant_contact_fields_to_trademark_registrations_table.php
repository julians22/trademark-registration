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
            if (! Schema::hasColumn('trademark_registrations', 'applicant_name')) {
                $table->string('applicant_name')->nullable()->after('applicant_type');
            }

            if (! Schema::hasColumn('trademark_registrations', 'applicant_company')) {
                $table->string('applicant_company')->nullable()->after('applicant_name');
            }

            if (! Schema::hasColumn('trademark_registrations', 'applicant_email')) {
                $table->string('applicant_email')->nullable()->after('applicant_company');
            }

            if (! Schema::hasColumn('trademark_registrations', 'active_phone_number')) {
                $table->string('active_phone_number')->nullable()->after('applicant_email');
            }

            if (! Schema::hasColumn('trademark_registrations', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('active_phone_number');
            }

            if (! Schema::hasColumn('trademark_registrations', 'wechat_number')) {
                $table->string('wechat_number')->nullable()->after('whatsapp_number');
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

            if (Schema::hasColumn('trademark_registrations', 'wechat_number')) {
                $dropColumns[] = 'wechat_number';
            }

            if (Schema::hasColumn('trademark_registrations', 'whatsapp_number')) {
                $dropColumns[] = 'whatsapp_number';
            }

            if (Schema::hasColumn('trademark_registrations', 'active_phone_number')) {
                $dropColumns[] = 'active_phone_number';
            }

            if (Schema::hasColumn('trademark_registrations', 'applicant_email')) {
                $dropColumns[] = 'applicant_email';
            }

            if (Schema::hasColumn('trademark_registrations', 'applicant_company')) {
                $dropColumns[] = 'applicant_company';
            }

            if (Schema::hasColumn('trademark_registrations', 'applicant_name')) {
                $dropColumns[] = 'applicant_name';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
