<?php
require 'vendor/autoload.php';
$app=require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$t=\App\Models\Tagihan::find(33);
echo 'Tagihan 33 - Status: ' . $t->payment_status . PHP_EOL;
?>
