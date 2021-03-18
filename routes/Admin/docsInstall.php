<?php

use Rvsitebuilder\Larecipe\Http\Controllers\Admin\InstallDocsController;

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => 'web',
], function () {
    Route::group([
        'prefix' => 'larecipe',
        'as' => 'larecipe.',
        'middleware' => 'admin',
    ], function () {
        Route::group([
            'prefix' => 'docs',
            'as' => 'docs.',
            'middleware' => 'throttle',
        ], function () {
            Route::any('/install', [InstallDocsController::class, 'install'])->name('install');
        });
    });
});
