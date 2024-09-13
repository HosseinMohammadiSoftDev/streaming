<?php

namespace Modules\Straem\Http\Controllers;

use App\Http\Controllers\Contract\ApiController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StraemController extends ApiController
{
      public function show($filename)
    {
        $path = public_path("video/{$filename}");

        if (!file_exists($path)) {
            abort(404, "Video file not found.");
        }

        return view('straem::index', ['filename' => $filename]);
    }

     public function stream($filename)
    {
        $url = asset('video/' . $filename); // اگر از مسیر public استفاده می‌کنید
        return view('straem::index', ['url' => $url]);
    }

//     public function stream($filename)
//     {
//            $path = public_path("video/{$filename}");
// // dd($filename);
//            if (!file_exists($path)) {
//               abort(404, "Video file not found.");
//             }

//         $stream = function () use ($path) {
//             $stream = fopen($path, 'rb');
//             while (!feof($stream)) {
//                 echo fread($stream, 1024 * 8);
//                 ob_flush();
//                 flush();
//             }
//             fclose($stream);
//         };

//         return response()->stream($stream, 200, [
//             "Content-Type" => "video/mp4",
//             "Content-Length" => filesize($path),
//             "Content-Disposition" => "inline; filename='{$filename}'"
//         ]);
//     }
}
