<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('enterprise:about', function () {
    $this->info('Laravel Enterprise Kit by Aya Aljaidi');
})->purpose('Show project information');
