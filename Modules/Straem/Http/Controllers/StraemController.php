<?php

namespace Modules\Straem\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StraemController extends ApiController
{
    public function show($videoId)
    {       
        $videoPath = "stream/{$videoId}";
        $updatedContent = $this->generateTemporaryUrls($videoPath);
    
        $tempPath = "temp/{$videoId}";
        Storage::disk('liara')->put($tempPath, $updatedContent);
    
        $url = Storage::disk('liara')->temporaryUrl($tempPath, now()->addMinutes(60));

        return view('straem::index', compact('url'));
    }
    
    private function generateTemporaryUrls($videoPath)
    {
        $disk = Storage::disk('liara');
    
        $content = Storage::disk('liara')->get($videoPath);
        $lines = explode("\n", $content);
    
        foreach ($lines as &$line) {
            if (strpos($line, '.ts') !== false) {
                $tsPath = trim($line);
                $temporaryTsUrl = $disk->temporaryUrl("stream/{$tsPath}", Carbon::now()->addMinutes(60));
                $line = $temporaryTsUrl;
            }
        }

        return implode("\n", $lines);
    }
    
}
