<?php

namespace Rvsitebuilder\Larecipe\Http\Controllers\User;

use BinaryTorch\LaRecipe\DocumentationRepository;
use BinaryTorch\LaRecipe\Http\Controllers\DocumentationController;

class RvDocumentationController extends DocumentationController
{
    public function __construct(DocumentationRepository $documentationRepository)
    {
        $this->documentationRepository = $documentationRepository;
        parent::__construct($documentationRepository);
    }

    public function show($version, $pageWithLang = null)
    {
        // $pageWithLang = languages / page_name
        $pattern = '/(\/\w*).*/';
        $languages = preg_replace($pattern, '', $pageWithLang);
        $versionWithLang = $version . '/' . $languages . '/';
        preg_match($pattern, $pageWithLang, $aPage);

        $page = $aPage[0] ?? 'overview';

        // call this first to set $documentation->index from $versionWithLang/index.md
        $documentation = $this->documentationRepository->get($versionWithLang);
        $index = $documentation->index;

        // get page data from $version/$pageWithLang.md
        $documentation = $this->documentationRepository->get($version, $pageWithLang);

        if ($this->documentationRepository->isNotPublishedVersion($version)) {
            return redirect()->route(
                'larecipe.show',
                [
                    'version' => config('rvsitebuilder/larecipe.versions.default'),
                    'page' => ($page == 'overview') ? config('rvsitebuilder/larecipe.docs.landing') : $languages . $page,
                ]
            );
        }

        // Somehow $documentation->statusCode always return 404 which is incorrect
        if ($documentation->title == 'Page not found') {
            $rvStatusCode = 404;
        } else {
            $rvStatusCode = 200;
        }

        return response()->view('rvsitebuilder/larecipe::user.docs', [
            'page' => $page,
            'title' => $documentation->title,
            'index' => $index,
            'content' => $documentation->content,
            'currentVersion' => $version,
            'currentLang' => $languages,
            'versions' => $documentation->publishedVersions,
            'currentSection' => $documentation->currentSection,
            'canonical' => $documentation->canonical,
            'rvStatusCode' => $rvStatusCode
        ], $rvStatusCode);
    }

    public function getsidebar($version, $pageWithLang = null)
    {
        // $pageWithLang = languages / page_name
        $pattern = '/(\/\w*).*/';
        $languages = preg_replace($pattern, '', $pageWithLang);
        $versionWithLang = $version . '/' . $languages . '/';

        //get nav bar [ $versionWithLang == versions/languages ]
        $documentation = $this->documentationRepository->get($versionWithLang);
        $content = $documentation->index;

        //case: version isNotPublished
        if ($this->documentationRepository->isNotPublishedVersion($version)) {
            // Log::debug('isNotPublishedVersion: ' . $version);
            $version = $documentation->publishedVersions[0] ?? 'master';
            $versionWithLang = $version . '/' . $languages . '/';

            //get nav bar [ $versionWithLang == versions/languages ]
            $documentation = $this->documentationRepository->get($versionWithLang);
            $content = $documentation->index;
        }

        $userDocUrl = config('rvsitebuildercms.userdocument');
        $content = preg_replace('/src="\/storage\//', "src=\"$userDocUrl/storage/", $content);

        return response($content);
    }
}
