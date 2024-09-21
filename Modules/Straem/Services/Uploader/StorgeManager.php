<?php 

namespace Modules\Straem\Services\Uploader;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Straem\Jobs\Storage\PutFileAsHost;
use Modules\Straem\Jobs\Storage\PutFileAsPrivate;
use Modules\Straem\Jobs\Storage\PutFileAsPublic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class StorgeManager implements ShouldQueue
{ 
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function putFileAsPrivate(string $name, UploadedFile $file,string $type)
    {
        return Storage::disk('private')->putFileAs($type, $file, $name);
        // PutFileAsPrivate::dispatch($name, $file, $type);
    }
    
    public function putFileAsPublic(string $name, UploadedFile $file,string $type)
    {
        return Storage::disk('public')->putFileAs($type, $file, $name);
        // PutFileAsPublic::dispatch($name, $file, $type);
    }
    
    public function putFileAsHost(string $name, $fileContent,string $type)
    {   
        // return Storage::disk('liara')->putFileAs($type, $file, $name);
   
        $newName = str_replace(' ', '_', $name);
        return Storage::disk('liara')->put($this->directoryPrefix($type, $newName), $fileContent);
    }

    public function getAbsolutePathOf(string $name, string $type, bool $isPrivate)
    {

        return $this->disk($isPrivate)->Path($this->directoryPrefix($type, $name));

    }

    public function isFileExists(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->exists($this->directoryPrefix($type, $name));
    }

    public function getFile(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->download($this->directoryPrefix($type, $name));
    }


    public function deleteFile(string $name, string $type, bool $isPrivate)
    {
        return $this->disk($isPrivate)->delete($this->directoryPrefix($type, $name));
    }


    private function directoryPrefix($type , $name)
    {
        return $type . DIRECTORY_SEPARATOR . $name;
    }




    private function disk(bool $isPrivate)
    {
        return $isPrivate ? Storage::disk('private') : Storage::disk('public');
    }


}