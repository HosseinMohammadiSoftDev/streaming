<?php

namespace Modules\Straem\Services\Straem;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use Modules\Straem\Services\Straem\ConvertVideo;

class Video
{

    public function convertVideo($filepath, $fileName)
    {
        ini_set('MAX_EXECUTION_TIME', '-1');
        $formats = $this->getVideoFormats($filepath);

        $resize = [];
        foreach ($formats as $format) {
            array_push($resize, $format['convert']);
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'video_');
        copy($filepath, $tempFilePath);

        dispatch(new ConvertVideo($tempFilePath, $resize, $fileName));

    }


    public function getVideoFormats($filepath)
    {

        $ffprobe = FFProbe::create(
            [
            'ffprobe.binaries' => config(key: 'services.ffmpeg.ffprobe_path'),
            'ffmpeg.binaries' => config(key: 'services.ffmpeg.ffmpeg_path')
            ]
        );

        $resoloution = $ffprobe
            ->streams($filepath)
            ->videos()
            ->first()
            ->get('width')
            . 'x' .
            $ffprobe
            ->streams($filepath)
            ->videos()
            ->first()
            ->get('height');


        if ($resoloution == '1920x1080') {
            $formats = [
                [
                    'rate' => '4096',
                    'resoloution' => '1080p',
                    'convert' => '1080',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(1920, 1080),
                    'dim1' => 1920,
                    'dim1' => 1080,
                ],
                [
                    'rate' => '2048',
                    'resoloution' => '720p',
                    'convert' => '720',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(1280, 720),
                    'dim1' => 1280,
                    'dim1' => 720,
                ],
                [
                    'rate' => '750',
                    'resoloution' => '480p',
                    'convert' => '480',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(854, 480),
                    'dim1' => 854,
                    'dim1' => 480,
                ],
                [
                    'rate' => '276',
                    'resoloution' => '360p',
                    'convert' => '360',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(480, 360),
                    'dim1' => 480,
                    'dim1' => 360,
                ],
            ];
        } else if ($resoloution == '1280x720') {
            $formats = [
                [
                    'rate' => '2048',
                    'resoloution' => '720p',
                    'convert' => '720',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(1280, 720),
                    'dim1' => 1280,
                    'dim1' => 720,
                ],
                [
                    'rate' => '750',
                    'resoloution' => '480p',
                    'convert' => '480',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(854, 480),
                    'dim1' => 854,
                    'dim1' => 480,
                ],
                [
                    'rate' => '276',
                    'resoloution' => '360p',
                    'convert' => '360',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(480, 360),
                    'dim1' => 480,
                    'dim1' => 360,
                ],
            ];
        } else if ($resoloution == '854x480') {
            $formats = [
                [
                    'rate' => '750',
                    'resoloution' => '480p',
                    'convert' => '480',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(854, 480),
                    'dim1' => 854,
                    'dim1' => 480,
                ],
                [
                    'rate' => '276',
                    'resoloution' => '360p',
                    'convert' => '360',
                    'format' => new X264('aac', 'libx264', 'mp4'),
                    'dimensions' => new Dimension(480, 360),
                    'dim1' => 480,
                    'dim1' => 360,
                ],
            ];
        } else {
            $formats = [];
        }

        return $formats;
    }


    public function convertToResoloution($filepath, $format)
    {
        $uuid = uniqid();
        $generated_video_name = "{$uuid}-{$format['resoloution']}.mp4";

        $output = public_path("encode/{$generated_video_name}");

        $resizeFilter = new ResizeFilter($format['dimensions'], ['-crf', '23']);

        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($filepath);

        $formats = new X264('libmp3lame', 'libx264');
        $formats->setKiloBitrate($format['rate']);
        $video->addFilter($resizeFilter)->save($formats, $output);

        return (object)['success' => true, 'convertedVideoName' => $generated_video_name];
    }
}
