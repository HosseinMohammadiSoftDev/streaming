<?php

namespace Modules\Straem\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Straem\Entities\File;
use Modules\Straem\Http\Requests\File\UploadFileRequest as FileUploadFileRequest;
use Modules\Straem\Http\Requests\Role\UploadFileRequest;
use Modules\Straem\Services\Uploader\Uploader;

class FileController extends ApiController
{
    private $uploader;


    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;

    }


    public function index()
    {
        $files = File::all();

        return $this->respondSuccess('فایل ها با موفقت نمایش پیدا کردند', $files);
    }

    public function show(File $file)
    {
        dd($file);
        return $file->download();
    }

    public function delete(File $file)
    {
        dd('test');
        $file->delete();

        return $this->respondSuccess('فایل با موفقیت حذف شد.', $file);
    }


    public function new(FileUploadFileRequest $request)
    {
        // dd($request);
        // try{
            $request->validated();

            $this->uploader->upload();

            return $this->respondSuccess('فایل با موفقیت اپلود شد', []);
        // }catch(\Exception $e){
        //     return $this->respondInternalError('در مسیر به مشکلی بر خوردیم');
        // }


    }

}
