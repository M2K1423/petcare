<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('workflow_status', 50)->default('awaiting_exam')->after('status');
            $table->timestamp('accepted_at')->nullable()->after('appointment_at');
            $table->timestamp('started_at')->nullable()->after('accepted_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('follow_up_at')->nullable()->after('completed_at');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('record_code')->nullable()->unique()->after('id');
            $table->decimal('temperature_c', 4, 1)->nullable()->after('doctor_id');
            $table->decimal('weight_kg', 5, 2)->nullable()->after('temperature_c');
            $table->unsignedSmallInteger('heart_rate_bpm')->nullable()->after('weight_kg');
            $table->text('abnormal_signs')->nullable()->after('symptoms');
            $table->text('preliminary_diagnosis')->nullable()->after('diagnosis');
            $table->text('final_diagnosis')->nullable()->after('preliminary_diagnosis');
            $table->string('pathology', 255)->nullable()->after('final_diagnosis');
            $table->string('severity_level', 50)->nullable()->after('pathology');
            $table->text('treatment_protocol')->nullable()->after('treatment');
            $table->text('disease_progress')->nullable()->after('treatment_protocol');
            $table->text('follow_up_plan')->nullable()->after('disease_progress');
            $table->json('service_orders')->nullable()->after('follow_up_plan');
            $table->json('prescriptions')->nullable()->after('service_orders');
            $table->json('procedures')->nullable()->after('prescriptions');
            $table->json('progress_logs')->nullable()->after('procedures');
            $table->timestamp('signed_off_at')->nullable()->after('progress_logs');
        });

        DB::table('appointments')
            ->where('status', 'completed')
            ->update([
                'workflow_status' => 'completed',
                'completed_at' => DB::raw('COALESCE(updated_at, created_at)'),
            ]);

        DB::table('appointments')
            ->where('status', 'pending')
            ->update([
                'workflow_status' => 'awaiting_exam',
            ]);

        DB::table('appointments')
            ->where('status', 'confirmed')
            ->update([
                'workflow_status' => 'awaiting_exam',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'record_code',
                'temperature_c',
                'weight_kg',
                'heart_rate_bpm',
                'abnormal_signs',
                'preliminary_diagnosis',
                'final_diagnosis',
                'pathology',
                'severity_level',
                'treatment_protocol',
                'disease_progress',
                'follow_up_plan',
                'service_orders',
                'prescriptions',
                'procedures',
                'progress_logs',
                'signed_off_at',
            ]);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'workflow_status',
                'accepted_at',
                'started_at',
                'completed_at',
                'follow_up_at',
            ]);
        });
    }
};
