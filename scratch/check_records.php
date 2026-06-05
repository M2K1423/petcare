<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Pet;
use App\Models\MedicalRecord;

$owners = User::whereHas('role', function($q) { $q->where('slug', 'owner'); })->get();
echo "=== OWNERS ===\n";
foreach ($owners as $owner) {
    echo "Owner ID: {$owner->id} | Name: {$owner->name} | Email: {$owner->email}\n";
    $pets = Pet::where('owner_id', $owner->id)->get();
    foreach ($pets as $pet) {
        $mrCount = MedicalRecord::where('pet_id', $pet->id)->count();
        echo "  -> Pet ID: {$pet->id} | Name: {$pet->name} | Breed: {$pet->breed} | Medical Records Count: {$mrCount}\n";
        if ($mrCount > 0) {
            $mrs = MedicalRecord::where('pet_id', $pet->id)->get();
            foreach ($mrs as $mr) {
                echo "     - Record ID: {$mr->id} | Date: {$mr->record_date} | Diagnosis: {$mr->diagnosis}\n";
            }
        }
    }
}
