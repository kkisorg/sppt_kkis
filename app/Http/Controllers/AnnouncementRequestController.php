<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\AnnouncementRequest;
use App\AnnouncementRequestHistory;
use App\Media;

class AnnouncementRequestController extends Controller
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
     * Display the list of announcement request
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $current_timestamp = $now->timestamp;
        $present_announcements = $user->announcement_request()
            ->where('event_timestamp', '>', $current_timestamp)
            ->orderBy('event_timestamp')
            ->get();
        return view('announcementrequest.index', ['present_announcements' => $present_announcements]);
    }

    /**
     * Display the create announcement request form
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Display new announcement form
        $user = Auth::user();
        $media = Media::where('is_active', true)->get();
        return view(
            'announcementrequest.create',
            ['default_organization_name' => $user->organization_name, 'media' => $media]
        );
    }

    /**
     * Insert a new announcement request into the database
     *
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request)
    {
        $organization_name = $request->input('organization-name');
        $title = $request->input('title');
        $content =  $request->input('content');
        $duration = $request->input('duration');
        $event_datetime = $request->input('event-datetime');
        $event_timestamp = Carbon::parse($event_datetime)->timestamp;
        $media = $request->input('media');

        $announcement_request = AnnouncementRequest::create([
            'revision_no' => 0,
            'organization_name' => $organization_name,
            'title' => $title,
            'content' => $content,
            'duration' => $duration,
            'event_timestamp' => $event_timestamp,
            'creator_id' => Auth::id()
        ]);
        $announcement_request->media()->attach($media);

        $announcement_request_history = AnnouncementRequestHistory::create([
            'announcement_request_id' => $announcement_request->id,
            'revision_no' => 0,
            'organization_name' => $organization_name,
            'title' => $title,
            'content' => $content,
            'duration' => $duration,
            'event_timestamp' => $event_timestamp,
            'creator_id' => Auth::id()
        ]);
        $announcement_request_history->media()->attach($media);

        return redirect('/announcement_request', 303)
            ->with('success_message', 'Pengumuman Anda telah berhasil dibuat.');
    }

    /**
     * Display the edit announcement request form
     *
     * @param Request $request
     * @param string $announcement_request_id
     * @return Response
     */
    public function edit(Request $request, string $announcement_request_id)
    {
        $user = Auth::user();

        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);
        // User is not allowed to edit someone else's $announcement request
        if ($announcement_request->creator_id !== $user->id) {
            abort(403);
        }

        $announcement_request->event_datetime =
            Carbon::createFromTimestamp($announcement_request->event_timestamp)->format('m/d/Y g:i A');

        $media = Media::where('is_active', true)->get();
        return view(
            'announcementrequest.edit',
            ['announcement_request' => $announcement_request, 'media' => $media]
        );
    }

    /**
     * Update an announcement request into the database
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $announcement_request_id = $request->input('id');
        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);
        // User is not allowed to edit someone else's $announcement request
        if ($announcement_request->creator_id !== $user->id) {
            abort(403);
        }

        $latest_revision_no = AnnouncementRequest::where('id', $announcement_request_id)->first()->revision_no;
        $organization_name = $request->input('organization-name');
        $title = $request->input('title');
        $content =  $request->input('content');
        $duration = $request->input('duration');
        $event_datetime = $request->input('event-datetime');
        $event_timestamp = Carbon::parse($event_datetime)->timestamp;
        $media = $request->input('media');

        AnnouncementRequest::where('id', $announcement_request_id)->update([
            'revision_no' => $latest_revision_no + 1,
            'organization_name' => $organization_name,
            'title' => $title,
            'content' => $content,
            'duration' => $duration,
            'event_timestamp' => $event_timestamp,
            'editor_id' => Auth::id()
        ]);

        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);
        $announcement_request->media()->sync($media);

        $announcement_request_history = AnnouncementRequestHistory::create([
            'announcement_request_id' => $announcement_request->id,
            'revision_no' => $latest_revision_no + 1,
            'organization_name' => $organization_name,
            'title' => $title,
            'content' => $content,
            'duration' => $duration,
            'event_timestamp' => $event_timestamp,
            'creator_id' => Auth::id()
        ]);
        $announcement_request_history->media()->attach($media);

        return redirect('/announcement_request/', 303)
            ->with('success_message', 'Pengumuman Anda telah berhasil diubah.');
    }

    /**
     * Display the view announcement request form
     *
     * @param Request $request
     * @param string $announcement_request_id
     * @return Response
     */
    public function view(Request $request, $announcement_request_id)
    {
        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);

        $announcement_request->event_datetime =
            Carbon::createFromTimestamp($announcement_request->event_timestamp)->format('l, j F Y, g:i a');
        $announcement_request->media = implode(', ', $announcement_request->media()->pluck('name')->toArray());

        // Get the revision list
        $revisions = $announcement_request->history()
            ->where('revision_no', '!=', $announcement_request->revision_no)
            ->orderBy('revision_no', 'desc')
            ->get();

        foreach($revisions as $revision) {
            $revision->event_datetime =
                Carbon::createFromTimestamp($revision->event_timestamp)->format('l, j F Y, g:i a');
            $revision->create_datetime = $revision->create_timestamp->format('l, j F Y, g:i a');
            $revision->media = implode(', ', $revision->media()->pluck('name')->toArray());

        }

        return view(
            'announcementrequest.view',
            ['announcement_request' => $announcement_request, 'revisions' => $revisions]
        );
    }

    /**
     * Delele an announcement request from the database
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, string $announcement_request_id)
    {
        $user = Auth::user();

        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);
        // User is not allowed to delete someone else's $announcement request
        if ($announcement_request->creator_id !== $user->id) {
            abort(403);
        }

        AnnouncementRequest::destroy($announcement_request_id);
        return redirect('/announcement_request', 303)
            ->with('success_message', 'Pengumuman telah berhasil dihapus.');
    }
}
