<?php
$medicines = DB::table('medicines')->get();
foreach($medicines as $m) {
    echo $m->name . ": " . $m->image_url . "\n";
}
