<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$medicines = DB::table('medicines')->get();
foreach($medicines as $m) {
    $name = strtolower($m->name);
    $url = '/images/medicines/amoxicillin.png'; // default pills
    if (strpos($name, 'syrup') !== false || strpos($name, 'solution') !== false || strpos($name, 'gel') !== false) {
        $url = '/images/medicines/syrup.png';
    } elseif (strpos($name, 'drop') !== false) {
        $url = '/images/medicines/eyedrops.png';
    } elseif (strpos($name, 'spray') !== false) {
        $url = '/images/medicines/spray.png';
    } elseif (strpos($name, 'cream') !== false || strpos($name, 'paste') !== false) {
        $url = '/images/medicines/cream.png';
    }
    DB::table('medicines')->where('id', $m->id)->update(['image_url' => $url]);
}
echo "DONE LOCAL!\n";
