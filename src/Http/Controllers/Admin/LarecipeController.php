<?php

namespace Rvsitebuilder\Larecipe\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

class LarecipeController extends Controller
{
    protected static $languages;

    protected static $versions;

    public function __construct()
    {
        static::$languages = config('rvsitebuilder.larecipe.languages.published');
        if (empty(config('rvsitebuilder.larecipe.languages.published'))) {
            static::$languages = [config('rvsitebuilder.larecipe.languages.default')];
        }

        static::$versions = config('rvsitebuilder.larecipe.versions.published');
        if (empty(config('rvsitebuilder.larecipe.versions.published'))) {
            static::$versions = [config('rvsitebuilder.larecipe.versions.default')];
        }
    }

    public function index()
    {
        $version = config('rvsitebuilder.larecipe.versions.default'); // docs
        $lang = config('rvsitebuilder.larecipe.languages.default');
        $path = base_path(config('rvsitebuilder.larecipe.docs.path')) . '/' . $version . '/' . $lang;

        $openDocs = false;
        $warning = true;
        if (is_dir($path)) {
            $openDocs = true;
        }

        $isFileOverview = $path . '/overview.md';
        $isFileIndex = $path . '/index.md';

        if (file_exists($isFileOverview) && file_exists($isFileIndex)) {
            $warning = false;
        }

        return view('rvsitebuilder/larecipe::admin.index', [
            'openDocs' => $openDocs,
            'warning' => $warning,
        ]);
    }

    public function getConfig(): Collection
    {
        $strLanguage = '';

        foreach (static::$languages as $language) {
            $strLanguage = $strLanguage ? $strLanguage . ',' . $language : $language;
        }

        $strVersion = '';

        foreach (static::$versions as $version) {
            $strVersion = $strVersion ? $strVersion . ',' . $version : $version;
        }
        return collect([
            'languages' => $strLanguage,
            'versions' => $strVersion,
        ]);
    }
}
