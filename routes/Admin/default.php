<?php

use Rvsitebuilder\Larecipe\Http\Controllers\Admin\LarecipeController;

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
        Route::get('/', [LarecipeController::class, 'index'])->name('index');
    });
});
