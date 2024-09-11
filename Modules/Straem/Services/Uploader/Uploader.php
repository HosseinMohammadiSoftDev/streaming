<?php 

namespace Modules\Straem\Services\Uploader;

use App\Exceptions\FileHasExistsException;
use Illuminate\Http\Request;
use Modules\Straem\Entities\File;
use Modules\Straem\Services\Straem\Video;
use Modules\Straem\Services\Uploader\FFMpegService;

class Uploader 
{
    private $request;
    private $storageManager;
    private $file;
    private $ffmpeg;


    public function __construct(Request $request, StorgeManager $storageManager, FFMpegService $ffmpeg)
    {
        $this->request = $request;
        $this->storageManager = $storageManager;
        $this->file = $request->file;
        $this->ffmpeg = $ffmpeg;
    }


    public function upload()
    {

        // if ($this->isFileExists()) throw new FileHasExistsException('فایل را مجدد نمیتوانید اپلود کنید');

        $this->putFileIntoStorage(); 

        return $this->saveFileIntoDatabase();

    }


    private function saveFileIntoDatabase()
    {
        $file = new File([
            'name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
            'type' => $this->getType(),
            'is_private' => $this->isPrivate()
        ]);
        
        $this->getStraem($file);

        $file->time = $this->getTime($file);
        
        $file->save();
    }


    private function getTime(File $file)
    {
        if (!$file->isMedia()) return null;

        return $this->ffmpeg->durationOf($file->absolutePath());
    }



    private function getStraem(File $file)
    {
        if (!$file->isMedia()) return null;

        return $this->ffmpeg->straem($file->absolutePath());
    }

    private function putFileIntoStorage()
    {
        $method = $this->isPrivate() ? 'putFileAsPrivate' : 'putFileAsPublic';

        $newFilePath = $this->storageManager->$method($this->file->getClientOriginalName(), $this->file,$this->getType());
    }


    private function isPrivate()
    {
        return $this->request->has('is_private');
    }

    private function getType()
    {
        return [
            'image/jpeg' => 'image',
            'video/mp4' => 'video',
            'application/zip' => 'archive'
        ][$this->file->getClientMimeType()];
    }

    private function isFileExists()
    {
       return $this->storageManager->isFileExists($this->file->getClientOriginalName(), $this->getType(), $this->isPrivate());
    }


}