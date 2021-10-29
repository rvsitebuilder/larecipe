<?php

namespace Rvsitebuilder\Larecipe\Http\Controllers\User;

use BinaryTorch\LaRecipe\Cache;
use BinaryTorch\LaRecipe\DocumentationRepository;
use BinaryTorch\LaRecipe\Http\Controllers\SearchController;
use Rvsitebuilder\Larecipe\Traits\RvIndexable;

class RvSearchController extends SearchController
{
    use RvIndexable;

    protected $documentationRepository;

    protected $cache;

    public function __construct(DocumentationRepository $documentationRepository, Cache $cache)
    {
        $this->cache = $cache;
        $this->documentationRepository = $documentationRepository;
        parent::__construct($documentationRepository);
    }

    /**
     * Get the index of a given version using $_SERVER['HTTP_REFERER'] to run on unpoly pages
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke($version = null, $lang = null)
    {
        if (!$version || !$lang) {
            $referrerPath = parse_url($_SERVER['HTTP_REFERER'])['path'];
            $path = secure_url(config('rvsitebuilder.larecipe.docs.route'));

            $documentPath = str_replace($path, '', $referrerPath);

            $pieces = explode('/', $documentPath);
            $version = $pieces[1];
            $lang = $pieces[2];
        }

        $this->authorizeAccessSearch($version);

        return response()->json(
            $this->rvIndex($version, $lang)
        );
    }
}
