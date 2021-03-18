<?php

namespace Rvsitebuilder\Larecipe\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class LarecipeController extends Controller
{
    public function index()
    {
        $version = config('rvsitebuilder/larecipe.versions.default'); // docs
        $lang = config('rvsitebuilder/larecipe.languages.default');
        $path = base_path(config('rvsitebuilder/larecipe.docs.path')).'/'.$version.'/'.$lang;

        $openDocs = false;
        $warning = true;
        if (is_dir($path)) {
            $openDocs = true;
        }

        $isFileOveeview = $path.'/overview.md';
        $isFileIndex = $path.'/index.md';

        if (file_exists($isFileOveeview) && file_exists($isFileIndex)) {
            $warning = false;
        }

        return view('rvsitebuilder/larecipe::admin.index', ['openDocs' => $openDocs, 'warning' => $warning]);
    }
}
