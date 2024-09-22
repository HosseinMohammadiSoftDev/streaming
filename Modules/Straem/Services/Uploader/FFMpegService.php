<?php

namespace Modules\Straem\Services\Uploader;

use Exception;
use FFMpeg\FFProbe;
use Modules\Straem\Services\Straem\Video;
use Streaming\Exception\Exception as ExceptionException;

class FFMpegService
{
    private $ffprobe;


    public function __construct()
    {
        $this->ffprobe = FFProbe::create([
            'ffprobe.binaries' => config('services.ffmpeg.ffprobe_path')
            // 'ffprobe.binaries' => "C:\\ffmpeg\\ffprobe.exe"
        ]);
    }   


    public function durationOf(string $path)
    {
        return (int) $this->ffprobe->format($path)->get('duration');
    }

    public function straemInLocal(string $path, $fileName)
    {
        $video_class = new Video;

        $result =  $video_class->convertVideo($path, $fileName);
    }

     public function straemInHost($url, $file)
    {
         $video_class = new Video;
    
        $fileContent = file_get_contents($url);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'video_');

        file_put_contents($tempFilePath, $fileContent);

        $fileName = $file->name; 

        $result = $video_class->convertVideo($tempFilePath, $fileName);

        unlink($tempFilePath);

        return $result;       

    }


}
