<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class APIController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle an image upload attempt
     *
     * @param Request $request
     * @return Response
     */
    public function image_upload(Request $request)
    {
        $now = Carbon::now();
        $user = Auth::user();

        // If user is not authenticated, reject the image upload request
        if (!$user) {
            return response()->json([
                'uploaded' => false,
                'error' => array('message' => 'Authentication required.')
            ]);
        }

        $file = $request->file('upload');
        $original_filename = $file->getClientOriginalName();
        $file_extension = strtolower($file->getClientOriginalExtension());
        $file_size = $file->getClientSize();

        // Only image (limited extension) is allowed
        $supported_extension = array('tiff', 'tif', 'jpeg', 'jpg', 'gif', 'png');
        if (!in_array($file_extension, $supported_extension)) {
            return response()->json([
                'uploaded' => false,
                'error' => array('message' => 'Only JPG, PNG, TIF and GIF are supported.')
            ]);
        }

        // Image size is 3MB maximum
        if ($file_size > 3145738) {
            return response()->json([
                'uploaded' => false,
                'error' => array('message' => 'Maximum file size is 3MB.')
            ]);
        }

        $filename = $user->id . '_' . $now->timestamp . '_' . preg_replace('/\s+/', '_', $original_filename);
        $upload_path = Storage::url(Storage::putFileAs('public/images', $file, $filename));
        return response()->json([
            'uploaded' => true,
            'url' => $upload_path
        ]);
    }
}
