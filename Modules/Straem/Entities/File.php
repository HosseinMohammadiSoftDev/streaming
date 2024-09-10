<?php

namespace Modules\Straem\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Straem\Services\Uploader\StorgeManager;

class File extends Model
{
    use HasFactory;


    protected $fillable = [
        'name' , 'size' , 'time' , 'type' , 'is_private'
    ];



    public function isMedia()
    {
        return $this->type == 'video';
    }


    public function absolutePath()
    {
        return resolve(StorgeManager::class)->getAbsolutePathOf($this->name, $this->type, $this->is_private);
    }

    public function download()
    {
        // dd('mothed download');
        // dd(resolve(StorgeManager::class)->getFile());
        // dd($this->is_private);
        return resolve(StorgeManager::class)->getFile($this->name, $this->type, $this->is_private);
    }

    public function delete()
    {
        resolve(StorgeManager::class)->deleteFile($this->name, $this->type, $this->is_private);

        parent::delete();

    }
    
    // protected static function newFactory()
    // {
    //     return \Modules\Straem\Database\factories\FileFactory::new();
    // }
}
