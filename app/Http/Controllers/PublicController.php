<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

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
    * @return Response
    */
    public function main_menu()
    {
        $user = Auth::user();
        return view('public.menu', ['user' => $user]);
    }
}
