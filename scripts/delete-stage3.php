<?php
// One-off script to delete existing LogbookStage entries with stage_number == 3
// Run this from project root: php scripts/delete-stage3.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LogbookStage;

echo "Deleting LogbookStage entries with stage_number = 3...\n";
$deleted = LogbookStage::where('stage_number', 3)->delete();
echo "Deleted: $deleted rows\n";
echo "Done. Restart your web server if needed.\n";

return 0;
