<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Product;

echo "=== ALL 21 PRODUCTS AND CURRENT CATEGORIES ===" . PHP_EOL;
foreach (Product::all() as $p) {
    echo "ID: {$p->id} | Slug: {$p->slug} | Name: {$p->name} => " . $p->categories->pluck('name')->join(', ') . PHP_EOL;
}
