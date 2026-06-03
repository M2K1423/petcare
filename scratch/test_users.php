<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::with('role')->get(['id', 'name', 'email', 'role_id', 'is_locked']);
foreach ($users as $u) {
    $roleSlug = $u->role->slug ?? 'no role';
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: {$roleSlug} | Locked: " . ($u->is_locked ? 'YES' : 'NO') . "\n";
}
