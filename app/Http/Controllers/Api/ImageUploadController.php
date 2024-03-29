<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // validating the file.
        validator($request->all(),[
            'file' => 'required|mimes:jpg,png,gif'
        ])->validate();
        
        // $file = $request->file('file')->store('public/images');
        $img = $request->file('file');
        try{
            // using the cloud config disk
            $file = Storage::disk('cloud')->put('/java',$img);
            // using the s3 config
            $file = Storage::disk('s3')->put('/public/images',$img);
        }catch(Exception $e)
        {
            // dd($e->getMessage());
            return response()->json([
                "status" => 400,
                "message" => "file upload failed!",
                "data" => [
                    $e->getMessage()
                ]
            ]);
        }
        return response()->json([
            "status" => 200,
            "message" => "file upload success",
            "data" => [
                $file
            ]
        ]);
    }
}
