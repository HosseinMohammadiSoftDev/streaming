<?php

namespace Modules\Straem\Services\Straem;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Straem\Entities\File;
use Modules\Straem\Services\Straem\Video;
use Streaming\FFMpeg;
// use Streaming\FFMpeg;

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

    public function handle(): void
    {
        ini_set('MAX_EXECUTION_TIME', '-1');

            // ffmpeg streaming
        $ffmpeg = FFMpeg::create([
            'timeout' => 3600,
        ]);
        
        $video = $ffmpeg->open($this->filepath);
        
        $video->hls()
            ->x264()
            ->autoGenerateRepresentations($this->resize)
            ->save(public_path('video/2/' . str_replace(' ', '_', $this->fullName)));


        // $video_model = new File;
        // dd('name');
        // $video_model->video = str_replace(' ', '_', $this->fullName);
        // $video_model->save();
    }
}
