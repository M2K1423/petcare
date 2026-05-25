<?php

$medicines = DB::table('medicines')->get();
foreach($medicines as $m) {
    $name = strtolower($m->name);
    $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Pills_1.jpg/320px-Pills_1.jpg'; // default pills
    if (strpos($name, 'syrup') !== false || strpos($name, 'solution') !== false || strpos($name, 'gel') !== false) {
        $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Cough_syrup.jpg/320px-Cough_syrup.jpg';
    } elseif (strpos($name, 'drop') !== false) {
        $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/Eye_drops.jpg/320px-Eye_drops.jpg';
    } elseif (strpos($name, 'spray') !== false) {
        $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Nasal_spray.jpg/320px-Nasal_spray.jpg';
    } elseif (strpos($name, 'amoxicillin') !== false) {
        $url = 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Amoxicillin_pills.jpg/320px-Amoxicillin_pills.jpg';
    }
    DB::table('medicines')->where('id', $m->id)->update(['image_url' => $url]);
}
echo "Updated all images!\n";
