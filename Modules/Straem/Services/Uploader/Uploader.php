<?php 

namespace Modules\Straem\Services\Uploader;

use App\Exceptions\FileHasExistsException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Straem\Entities\File;
use Modules\Straem\Jobs\Storage\PutFileAsHost;
use Modules\Straem\Jobs\Storage\StoreFile;
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

        // $this->putFileIntoStorage(); 

        $this->putFileInToHost();

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

        // $file->time = $this->getTime($file);
        
        $this->getStraemInHost($file);

        $file->save();
    }


    private function getTime(File $file)
    {
        if (!$file->isMedia()) return null;

        $filePath = $file->absolutePath();
    
        if (!file_exists($filePath)) {
            throw new \Exception("File does not exist: " . $filePath);
        }
    
        return $this->ffmpeg->durationOf($filePath);
    }


    private function getStraemInLocal(File $file)
    {
        if (!$file->isMedia()) return null;

        return $this->ffmpeg->straemInLocal($file->absolutePath(), $file->name);
    }
        
    
    private function getStraemInHost($file)
    {
        if (!$file->isMedia()) return null;

        $preSignedUrl = $this->getPreSignedUrl($file->name);

        return $this->ffmpeg->straemInHost($preSignedUrl, $file);
    }


    private function putFileIntoStorage()
    {
        $method = $this->isPrivate() ? 'putFileAsPrivate' : 'putFileAsPublic';

        $newFilePath = $this->storageManager->$method($this->file->getClientOriginalName(), $this->file,$this->getType());
    }

    private function putFileInToHost()
    {
        // $this->storageManager->putFileAsHost($this->file->getClientOriginalName(), $this->file, $this->getType());

        $filePath = $this->file->getRealPath();
        $tempFileCopy = tempnam(sys_get_temp_dir(), 'copy_');
        copy($filePath, $tempFileCopy);

        StoreFile::dispatch('putFileAsHost', [$this->file->getClientOriginalName(), $tempFileCopy, $this->getType()]);
    }
    
    private function isPrivate()
    {
        return $this->request->is_private;
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

    public function getPreSignedUrl($fileName)
    {
        $disk = Storage::disk('liara'); 
        $expiry = now()->addMinutes(10);

        $url = $disk->temporaryUrl('video/' . $fileName, $expiry);
dd($fileName);
        return $url;
    }


}