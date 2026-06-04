<?php
$url = 'https://ai-service-pet.onrender.com/api/v1/chat';
$apiKey = 'petcare-ai-key';

$payload = [
    'user_id' => 'test_user_local',
    'message' => 'Mèo của tôi bị biếng ăn thì phải làm sao?',
    'context' => [
        'customer_name' => 'Phan Phat',
        'role' => 'owner',
        'pet_type' => 'Cat',
        'pet_age' => '3',
        'vaccination' => 'Da tiem'
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 35); // 35 seconds timeout

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $http_code\n";
if ($error) {
    echo "Error: $error\n";
} else {
    echo "Response:\n$response\n";
}
