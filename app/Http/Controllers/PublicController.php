<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Media;
use App\UserActivityTracking;

class PublicController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the main menu
     *
     * @param Request $request
     * @return Response
     */
    public function main_menu(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'MAIN_MENU',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        $user = Auth::user();

        $offline_media_name_array = Media
            ::has('offline_media')
            ->where('is_active', true)
            ->pluck('name')->toArray();
        $offline_media_name = join(', ', $offline_media_name_array);

        return view(
            'public.menu',
            ['user' => $user, 'offline_media_name' => $offline_media_name]
        );
    }
}
