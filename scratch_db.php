<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChatSession;
use App\Models\ChatMessage;

$session = ChatSession::orderBy('updated_at', 'desc')->first();
if ($session) {
    echo "=== ACTIVE CHAT SESSION ===\n";
    echo "ID: " . $session->id . "\n";
    echo "Owner ID: " . $session->owner_id . "\n";
    echo "Staff ID: " . $session->staff_id . "\n";
    echo "Status: " . $session->status . "\n";
    
    $staff = \App\Models\User::with('role')->find($session->staff_id);
    if ($staff) {
        echo "Staff Name: " . $staff->name . "\n";
        echo "Staff Role: " . ($staff->role->slug ?? 'None') . "\n";
    } else {
        echo "Staff not found!\n";
    }

    $messages = ChatMessage::where('chat_session_id', $session->id)->orderBy('created_at', 'desc')->take(5)->get();
    echo "\n=== RECENT MESSAGES ===\n";
    foreach ($messages as $msg) {
        echo "Sender ID: " . $msg->sender_id . " | Body: " . $msg->body . "\n";
    }
} else {
    echo "No chat sessions found!\n";
}
