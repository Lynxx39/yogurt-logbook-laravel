<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LogbookStage;
$stages = LogbookStage::all();
foreach ($stages as $s) {
    echo "---\nID: {$s->id} | stage: {$s->stage_number}\n";
    print_r($s->data);
    echo "\n";
}

echo "Done.\n";
