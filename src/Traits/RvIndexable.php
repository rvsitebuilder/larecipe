<?php

namespace Rvsitebuilder\Larecipe\Traits;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\DomCrawler\Crawler;

trait RvIndexable
{
    public function rvIndex($version, $lang = 'null')
    {
        $versionWithLang = $lang ? $version . '/' . $lang : $version;

        return $this->cache->remember(function () use ($versionWithLang, $version, $lang) {
            $pages = $this->rvGetPages($versionWithLang);

            $result = [];
            foreach ($pages as $page) {
                $pageWithLang = $lang ? $lang . '/' . $page : $page;

                $pageContent = $this->documentationRepository->get($version, $pageWithLang);

                if ($pageContent->statusCode != 200 || !$pageContent->content) {
                    continue;
                }

                $indexableNodes = implode(',', config('larecipe.search.engines.internal.index'));

                $nodes = (new Crawler($pageContent->content))
                    ->filter($indexableNodes)
                    ->each(function (Crawler $node, $i) {
                        return $node->text();
                    });

                $title = (new Crawler($pageContent->content))
                    ->filter('h1')
                    ->each(function (Crawler $node, $i) {
                        return $node->text();
                    });

                $result[] = [
                    'path' => $page,
                    'title' => $title ? $title[0] : '',
                    'headings' => $nodes,
                ];
            }

            return $result;
        }, 'larecipe.docs.' . $versionWithLang . '.search');
    }

    /**
     * @return mixed
     */
    protected function rvGetPages($versionWithLang)
    {
        $path = base_path(config('rvsitebuilder.larecipe.docs.path')) . '/' . $versionWithLang . '/index.md';
        $files = new Filesystem();

        preg_match_all('/\(([^)]+)\)/', $files->get($path), $matches);

        return $matches[1];
    }
}
