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

class ConvertVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        $fileName = str_replace(' ', '_', $this->fullName);

        $tempDir = public_path('temp');

        $video->hls()
            ->x264()
            ->autoGenerateRepresentations($this->resize)
      ->save($tempDir . $fileName
            // رمز نگاری AES-128 
                // '-hls_key_info_file' => public_path('key_info_file.txt'),
                // '-hls_time' => 10,
                // '-hls_playlist_type' => 'vod',
                // '-hls_segment_filename' => $tempDir . '/segment_%03d.ts'
            );

        $files = glob($tempDir . '/*');
            
        foreach ($files as $file) {
            $relativePath = 'stream/' . basename($file);

            $content = file_get_contents($file);
            Storage::disk('liara')->put($relativePath, $content);
        }

        $this->deleteDirectory($tempDir);

    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        if (!is_dir($dir)) {
            unlink($dir);
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $this->deleteDirectory("$dir/$file");
        }

        rmdir($dir);
    }

} 
