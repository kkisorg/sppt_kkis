<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Media;

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
