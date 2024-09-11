<?php

namespace Modules\Straem\Services\Straem;

use FFMpeg\FFMpeg;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Straem\Services\Straem\Video;
// use Streaming\FFMpeg;

class ConvertVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $resize;
    public $filepath;
    public $episode_title;
    public $episode_id;


    public $timeout = 3600;

    public function __construct($filepath, $resize, $episode_title, $episode_id)
    {
        $this->resize = $resize;
        $this->filepath = $filepath;
        $this->episode_title = $episode_title;
        $this->episode_id = $episode_id;
    }

    public function handle(): void
    {
        ini_set('MAX_EXECUTION_TIME', '-1');

        $ffmpeg = FFMpeg::create([
            'timeout' => 3600,
        ]);
        $video = $ffmpeg->open($this->filepath);

        $video->hls()
            ->x264()
            ->autoGenerateRepresentations($this->resize)
            ->save(public_path('../../../website/public_html/academy/videos/' . str_replace(' ', '_', $this->episode_title) . '/' . str_replace(' ', '-', $this->episode_title)));

        $video_model = new Video;
        $video_model->episode_id = $this->episode_id;
        $video_model->video = str_replace(' ', '_', $this->episode_title) . '/' . str_replace(' ', '-', $this->episode_title);
        $video_model->save();
    }
}
