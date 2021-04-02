<?php

use Rvsitebuilder\Larecipe\Http\Controllers\User\RvDocumentationController;
use Rvsitebuilder\Larecipe\Http\Controllers\User\RvSearchController;

Route::group([
    'prefix' => config('rvsitebuilder.larecipe.docs.route'),
    'domain' => config('rvsitebuilder.larecipe.domain', null),
    'as' => 'larecipe.',
    'middleware' => 'web',
], function () {
    // Built-in Search..
    Route::get('/search-index/{version?}/{lang?}', [RvSearchController::class])->name('search');

    // Documentation..
    Route::get('/', [RvDocumentationController::class, 'index'])->name('index');
    Route::get('/{version?}/{page?}', [RvDocumentationController::class, 'show'])->where('page', '(.*)')->name('show');
});


Route::group([
    'prefix' => 'getsidebar',
    'as' => 'getsidebar.',
    'middleware' => 'web',
], function () {
    Route::get('/{version}/{page?}', [RvDocumentationController::class, 'getsidebar'])->where('page', '(.*)')->name('getsidebar');
});
