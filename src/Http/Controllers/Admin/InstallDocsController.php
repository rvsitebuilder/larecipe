<?php

namespace Rvsitebuilder\Larecipe\Http\Controllers\Admin;

use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class InstallDocsController extends Controller
{
    protected $filesystem;
    protected $message = 'Error';
    protected $branchName = '';

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function install()
    {
        // last slash (/)
        $this->branchName = substr(strrchr(rtrim(config('rvsitebuilder/larecipe.github'), '/'), '/'), 1);
        $publishedVersions = config('rvsitebuilder/larecipe.versions.published');
        $publishedLanguage = config('rvsitebuilder/larecipe.languages.published');

        if (empty($publishedLanguage)) {
            $publishedLanguage = config('rvsitebuilder/larecipe.languages.default');
        }

        // delete old file
        Log::debug(__METHOD__.' delete old directory docs file');

        $mainFolder = base_path('/public/storage/larecipe');
        $publicHtmlFolder = public_path('storage/larecipe');

        if ($this->filesystem->isDirectory($mainFolder) || $this->filesystem->isDirectory($publicHtmlFolder)) {
            $this->filesystem->deleteDirectory($mainFolder);
            $this->filesystem->deleteDirectory($publicHtmlFolder);
        }

        $respone = true;
        foreach ($publishedVersions as $version) {
            foreach ($publishedLanguage as $language) {
                $versionDirectory = config('rvsitebuilder/larecipe.docs.path').'/'.$version.'/'.$language;

                // production read image at public_html only.
                $this->createVersionDirectory(public_path('storage/larecipe/docs/').$version.'/'.$language);

                $respone = $this->createVersionDirectory(base_path($versionDirectory));

                if ($respone == false) {
                    return response()->json(['message' => $this->message], 404);
                }

                // download zip file from git
                $respone = $this->download_docs_file($version);
                if ($respone == false) {
                    Log::error('download fail: '.$this->message);

                    return response()->json(['message' => $this->message], 404);
                }

                // extract zip file
                $respone = $this->unzip_docs_file($version);
                if ($respone == false) {
                    Log::error('unzip fail: '.$this->message);

                    return response()->json(['message' => $this->message], 404);
                }

                // move
                $respone = $this->move_docs_file($version, $language);
                if ($respone == false) {
                    Log::error('move fail: '.$this->message);

                    return response()->json(['message' => $this->message], 404);
                }

                // modify index.md
                $respone = $this->modify_index($version, $language);

                if ($respone == false) {
                    Log::error('modify_index fail: '.$this->message);

                    return response()->json(['message' => $this->message], 404);
                }
            }
            // delete old zip file
            $mainFolder = base_path('/public/storage/larecipe/').$this->branchName.'-'.$version;
            if ($this->filesystem->isDirectory($mainFolder)) {
                $this->filesystem->deleteDirectory($mainFolder);
            }
        }

        if (empty(config('rvsitebuilder/larecipe.github'))) {
            return redirect()->route('admin.larecipe.index');
        }
        
        return redirect()->route('admin.larecipe.index')->withFlashSuccess('Update Success');
    }

    protected function download_docs_file($version): bool
    {
        $url = config('rvsitebuilder/larecipe.github').'/archive/'.$version.'.zip';
        Log::debug(__METHOD__.' START download: '.$url);
        try {
            $guzzle = new Client();
            $response = $guzzle->get($url);
            Storage::put($version.'.zip', $response->getBody());

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            Log::error($e->getMessage().' at line:'.$e->getLine());

            return false;
        }
    }

    protected function unzip_docs_file($version): bool
    {
        Log::debug(__METHOD__.' START ');
        try {
            $zip = new ZipArchive();
            $zipFilePath = storage_path('app/').$version.'.zip';
            $extractPath = base_path('/public/storage/larecipe');

            Log::debug(__METHOD__.' START unzip: '.$zipFilePath);
            if ($zip->open($zipFilePath) != 'true') {
                Log::error('Unable to open the Zip File '.$zipFilePath);

                $this->message = 'Unable to open the Zip File '.$zipFilePath;

                return false;
            }

            if (is_file($zipFilePath)) {
                // Extract Zip File
                $zip->extractTo($extractPath);
                $zip->close();

                // delete zip file
                Log::debug(__METHOD__.' delete file: '.$zipFilePath);
                unlink($zipFilePath);
            }

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            Log::error($e->getMessage().' at line:'.$e->getLine());

            return false;
        }
    }

    protected function move_docs_file($version, $language): bool
    {
        Log::debug(__METHOD__.' START verion:'.$version);
        try {
            $docsDirectory = base_path('/public/storage/larecipe/').$this->branchName.'-'.$version.'/'.$language;

            if ($this->filesystem->isDirectory($docsDirectory)) {
                $mdAll = scandir($docsDirectory);
                if (!empty($mdAll)) {
                    foreach ($mdAll as $mdFile) {
                        if ($mdFile == '.' || $mdFile == '..') {
                            continue;
                        }

                        $currentFilePath = $docsDirectory.'/'.$mdFile;
                        $newFilePath = base_path(config('rvsitebuilder/larecipe.docs.path')).'/'.$version.'/'.$language.'/'.$mdFile;
                        $newFilePathImage = public_path('storage/larecipe/docs/').$version.'/'.$language.'/'.$mdFile;

                        if ($this->filesystem->isDirectory($currentFilePath)) {
                            Log::debug(' CopyDirectory '.$mdFile.' To '.$newFilePath);
                            $this->filesystem->copyDirectory($currentFilePath, $newFilePathImage);
                        } else {
                            if ($mdFile != 'index.md' && $mdFile != 'README.md') {
                                $res = $this->addALinkHeader($currentFilePath);

                                if ($res == false) {
                                    return false;
                                }
                            }
                            // replace "images path" in content
                            $patternImage = '/storage/larecipe/docs/'.$version.'/'.$language.'/images/';

                            $contents = file_get_contents($currentFilePath);
                            $content = str_replace('@', '@@', $contents);
                            $content = str_replace('{{', '@{{', $content);
                            $content = str_replace('.md', '', $content);
                            $content = str_replace('images/', $patternImage, $content);
                            $this->filesystem->put($currentFilePath, $content);
                            $this->filesystem->move($currentFilePath, $newFilePath);
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            Log::error($e->getMessage().' at line:'.$e->getLine());

            return false;
        }
    }

    protected function modify_index($version, $language): bool
    {
        Log::debug(__METHOD__.' START');
        try {
            $indexPath = base_path(config('rvsitebuilder/larecipe.docs.path')).'/'.$version.'/'.$language.'/index.md';

            if (is_file($indexPath)) {
                $content = file_get_contents($indexPath);
                $content = str_replace('.md', '', $content);
                $this->filesystem->put($indexPath, $content);
            }

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            Log::error($e->getMessage().' at line:'.$e->getLine());

            return false;
        }
    }

    protected function createVersionDirectory(string $versionDirectory): bool
    {
        Log::debug(__METHOD__.' START '.$versionDirectory);
        try {
            if (!$this->filesystem->isDirectory($versionDirectory)) {
                $this->filesystem->makeDirectory($versionDirectory, 0755, true);
            }

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            Log::error($e->getMessage().' at line:'.$e->getLine());

            return false;
        }
    }

    protected function addALinkHeader(string $path): bool
    {
        try {
            Log::debug('file: '.$path);
            $content = file_get_contents($path);
            // find ## and ###
            preg_match_all('/(### |## )+(.*)/', $content, $matchHeader);

            if (isset($matchHeader[2])) {
                foreach ($matchHeader[2] as $key => $header) {
                    $trimHeader = rtrim($header);

                    //replace single quota, slash /, ( )
                    $trimHeader = preg_replace('/\'|\/|\(|\)/', '', $trimHeader);

                    $newALink = urlencode(preg_replace('/[[:space:]]+/', '-', strtolower($trimHeader)));
                    $oldTagHeader = $matchHeader[0][$key] ?? '';

                    $newTagHeader = '<a name="'.$newALink.'"></a>';
                    $newTagHeader .= "\n".$oldTagHeader;

                    $content = str_replace($oldTagHeader, $newTagHeader, $content);
                }

                File::put($path, $content);
            }

            return true;
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            Log::error($e->getMessage().' at line:'.$e->getLine());

            return false;
        }
    }
}
