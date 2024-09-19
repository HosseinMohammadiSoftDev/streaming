<?php

namespace Modules\Straem\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StraemController extends ApiController
{ 
    public function show($videoId)
    {       
        $videoPath = "stream/{$videoId}";
        $url = Storage::disk('liara')->temporaryUrl($videoPath, now()->addMinutes(60));
        // $url = Storage::disk('liara')->get($videoPath);
// dd($url);
        return view('straem::index', compact('url'));

    }

    public function stream($filename)
    {
        $disk = Storage::disk('liara'); // استفاده از دیسک پیکربندی‌شده در config/filesystems.php
        $filePath = 'stream/' . $filename; // مسیر فایل در هاست دانلود

        if (!$disk->exists($filePath)) {
            abort(404, "Video file not found.");
        }

        $stream = function () use ($disk, $filePath) {
            $stream = $disk->getDriver()->readStream($filePath);
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        };

        $response = new StreamedResponse($stream);
    
        $response->headers->set('Content-Disposition', 'inline; filename="' . $filename . '"');
        $response->headers->set('Content-Type', $disk->mimeType($filePath));
        $response->headers->set('Content-Transfer-Encoding', 'inline');

        // dd($response);
        return $response;
    }
}
