<?php

namespace Modules\Straem\Jobs\Storage;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Straem\Services\Uploader\StorgeManager;

class StoreFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $timeout = 3600;
    protected $method;
    protected $parameters;

    public function __construct(string $method, array $parameters)
    {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function handle()
    {
        $storageManager = new StorgeManager();
        $parameters = $this->parameters;
    
        $filePath = $parameters[1];


        $fileContent = file_get_contents($filePath);
    
        call_user_func_array([$storageManager, $this->method], array_merge([$parameters[0], $fileContent, $parameters[2]]));
    }
}
