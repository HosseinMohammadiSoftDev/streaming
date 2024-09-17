<?php

namespace Modules\Straem\Services\Straem;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Straem\Entities\File;
use Modules\Straem\Services\Straem\Video;
use Streaming\FFMpeg;
use Streaming\Stream;

class ConvertVideo 
{
    // implements ShouldQueue
   /* use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; */

    public $resize;
    public $filepath;
    public $fullName;

    public $timeout = 3600;

    public function __construct($filepath, $resize, $fileName)
    {
        $this->resize = $resize;
        $this->filepath = $filepath;
        $this->fullName = $fileName;
    }

    public function handle()
    {
        ini_set('MAX_EXECUTION_TIME', '-1');

            // ffmpeg streaming
        $ffmpeg = FFMpeg::create([
            'timeout' => 3600,
        ]);

        $video = $ffmpeg->open($this->filepath);
        $tempDir =  public_path('temp/' . str_replace(' ', '_', pathinfo($this->fullName, PATHINFO_FILENAME)));


        $video->hls()
            ->x264()
            ->autoGenerateRepresentations($this->resize)
            ->save($tempDir . '/playlist.m3u8');

         $files = glob($tempDir . '/*'); // دریافت همه فایل‌ها از پوشه موقت

        foreach ($files as $file) {
            // $relativePath = 'stream/' . basename($file);
            // Storage::disk('liara')->put($relativePath, file_get_contents($file));
        }
    }   
} 
