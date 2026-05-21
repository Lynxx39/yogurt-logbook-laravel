<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

$path = 'logbook-photos/d3gELhFjEq2aOMF8v1IVIYkn2pHzJAv9OutIh9Ww.png';
echo "Storage::url(\"$path\") => ", Storage::url($path), PHP_EOL;

echo "Exists in disk(public)? ", Storage::disk('public')->exists($path) ? 'yes' : 'no', PHP_EOL;
