<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Appointment;
use App\Models\MedicalRecord;

$appointments = Appointment::with(['pet', 'owner', 'doctor.user', 'medicalRecord'])->latest()->get();
echo "=== ALL APPOINTMENTS ===\n";
foreach ($appointments as $app) {
    $mr = $app->medicalRecord;
    echo "App ID: {$app->id} | Date: {$app->appointment_at} | Status: {$app->status} | Workflow: {$app->workflow_status}\n";
    echo "  -> Owner: ID {$app->owner_id} ({$app->owner?->name} - {$app->owner?->email})\n";
    echo "  -> Pet: ID {$app->pet_id} ({$app->pet?->name})\n";
    echo "  -> Doctor: " . ($app->doctor?->user?->name ?? 'None') . "\n";
    echo "  -> Medical Record: " . ($mr ? "ID {$mr->id} | Diagnosis: {$mr->diagnosis}" : 'None') . "\n";
    echo "---------------------------------------------------\n";
}
