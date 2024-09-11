<?php

namespace Modules\Straem\Http\Controllers;
    
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Institute\app\Models\InstituteEpisode;
use Modules\Straem\Entities\File;
use Modules\Straem\Services\Straem\Video;

class InstituteEpisodeController extends Controller
{

    public function create(Request $request)
    {
        ini_set('MAX_EXECUTION_TIME', '-1');
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimetypes:image/jpeg,video/mp4,application/zip'],
            // 'is_private' => ['required', 'in:1,0 else 0', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $episode = new File;
        $episode->file = $request->file;
        
        // $episode->image = $request->image;

        dd($episode);
        
        if ($episode->save()) {
            
            if ($request->file != null) {
                $video_class = new Video;
                dd('te');
                $result =  $video_class->convertVideo(public_path($request->file), $request->slug, $episode->id);
            }

            return response()->json($result, 200);
        } else {
            return response()->json(['status' => 'error',], 400);
        }
    }
}
