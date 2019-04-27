<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Media;
use App\OfflineMedia;
use App\OnlineMedia;

class MediaController extends Controller
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
     * Display the list of media
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        $media = Media::all();
        foreach ($media as $medium) {
            $medium->is_online = $medium->online_media != null;
        }
        return view('media.index', ['media' => $media]);
    }

    /**
     * Display the create media form
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        return view('media.create');
    }

    /**
     * Insert a new media into the database
     *
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
           abort(403);
        }

        $name = $request->input('name');
        $text = $request->input('text');
        $image = $request->input('image');
        $is_active = $request->input('is-active') === 'yes';
        $is_online = $request->input('is-online') === 'yes';

        $media = Media::create([
            'name' => $name,
            'text' => $text,
            'image' => $image,
            'is_active' => $is_active
        ]);

        // Create OnlineMedia or OfflineMedia
        $media_id = $media->id;
        if ($is_online) {
            OnlineMedia::create([
                'media_id' => $media_id
            ]);
        } else {
            OfflineMedia::create([
                'media_id' => $media_id
            ]);
        }

        return redirect('/media', 303)
            ->with('success_message', 'Media telah berhasil dibuat.');
    }

    /**
     * Display the edit media form
     *
     * @param Request $request
     * @param string $media_id
     * @return Response
     */
    public function edit(Request $request, string $media_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        $media = Media::findOrFail($media_id);
        $media->is_online = $media->online_media != null;
        return view('media.edit', ['media' => $media]);
    }

    /**
     * Update a media into the database
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $id = $request->input('id');
        $name = $request->input('name');
        $text = $request->input('text');
        $image = $request->input('image');
        $is_active = $request->input('is-active') === 'yes';
        $is_online = $request->input('is-online') === 'yes';

        Media::where('id', $id)->update([
            'name' => $name,
            'text' => $text,
            'image' => $image,
            'is_active' => $is_active
        ]);

        // Update OnlineMedia or OfflineMedia
        $media = Media::findOrFail($id);
        $media_id = $media->id;
        if ($is_online) {
            OfflineMedia::where('media_id', $media_id)->delete();
            OnlineMedia::firstOrCreate([
                'media_id' => $media_id
            ]);
        } else {
            OnlineMedia::where('media_id', $media_id)->delete();
            OfflineMedia::firstOrCreate([
                'media_id' => $media_id
            ]);
        }

        return redirect('/media', 303)
            ->with('success_message', 'Media telah berhasil diubah.');
    }

    /**
     * Delele a media from the database
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, string $media_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        Media::destroy($media_id);
        return redirect('/media', 303)
            ->with('success_message', 'Media telah berhasil dihapus.');
    }
}
