<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Announcement;
use App\AnnouncementRequest;
use App\Media;
use App\OfflineDistribution;

class AnnouncementController extends Controller
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
     * Display the list of announcement
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $now = Carbon::now();
        $current_timestamp = $now->timestamp;

        $present_announcements = Announcement
            ::where('event_timestamp', '>', $current_timestamp)
            ->orderBy('event_timestamp')
            ->get();

        return view('announcement.index', ['present_announcements' => $present_announcements]);
    }

    /**
     * Display the announcement approval center page
     *
     * @param Request $request
     * @return Response
     */
    public function approve(Request $request)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $now = Carbon::now();
        $current_timestamp = $now->timestamp;

        $approved_announcements = DB::select('
            select announcement.*
            from announcement
            where exists (
                select *
                from announcement_request
                where announcement_request.id = announcement.announcement_request_id
                and announcement_request.revision_no = announcement.revision_no
            )
            and event_timestamp > ?
            order by event_timestamp
        ', [$now->timestamp]);

        $new_announcement_requests = DB::select('
            select *
            from announcement_request
            where id not in (
                select distinct announcement_request_id
                from announcement
            )
            and event_timestamp > ?
            order by event_timestamp
        ', [$now->timestamp]);

        $revised_announcement_requests = DB::select('
            select announcement.*
            from announcement
            where exists (
                select *
                from announcement_request
                where announcement_request.id = announcement.announcement_request_id
                and announcement_request.revision_no > announcement.revision_no
            )
            and event_timestamp > ?
            order by event_timestamp
        ', [$now->timestamp]);

        return view('announcement.approve', [
            'approved_announcements' => $approved_announcements,
            'new_announcement_requests' => $new_announcement_requests,
            'revised_announcement_requests' => $revised_announcement_requests
        ]);
    }

    /**
     * Display the create announcement form
     *
     * @param Request $request
     * @param string $announcement_request_id
     * @return Response
     */
    public function create(Request $request, string $announcement_request_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        // Announcement request as a reference
        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);
        // Convert some details into the frontend format
        $announcement_request->event_datetime_human_readable =
            Carbon::createFromTimestamp($announcement_request->event_timestamp)->format('l, j F Y, g:i a');
        $announcement_request->event_datetime =
            Carbon::createFromTimestamp($announcement_request->event_timestamp)->format('m/d/Y g:i A');
        $announcement_request->media = implode(', ', $announcement_request->media()->pluck('name')->toArray());

        $media = Media::where('is_active', true)->get();
        return view('announcement.create', ['media' => $media, 'announcement_request' => $announcement_request]);
    }

    /**
     * Insert a new announcement into the database
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

        $now = Carbon::now();
        $announcement_request_id = $request->input('announcement-request-id');
        $revision_no = $request->input('revision-no');
        $organization_name = $request->input('organization-name');
        $title = $request->input('title');
        $content = $request->input('content');
        $duration = $request->input('duration');
        $event_datetime = $request->input('event-datetime');
        $event_timestamp = Carbon::parse($event_datetime)->timestamp;

        $announcement = Announcement::create([
            'announcement_request_id' => $announcement_request_id,
            'revision_no' => $revision_no,
            'organization_name' => $organization_name,
            'title' => $title,
            'content' => $content,
            'duration' => $duration,
            'creator_id' => Auth::id(),
            'event_timestamp' => $event_timestamp,
        ]);

        // Attach the chosen media
        foreach ($request->input('media') as $medium) {
            $content = $request->input('content-'.$medium);
            $media[$medium] = array('content' => $content);
        }
        $announcement->media()->attach($media);

        // Sync offline distribution
        $this->sync_offline_distribution($announcement->id);

        return redirect('/announcement/approve', 303)->with('success_message', 'Persetujuan pengumuman baru telah berhasil.');
    }

    /**
     * Display the edit announcement form
     *
     * @param Request $request
     * @param string $announcement_request_id
     * @return Response
     */
    public function edit(Request $request, string $announcement_request_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }
        $media = Media::where('is_active', true)->get();

        // Announcement request as a reference
        $announcement_request = AnnouncementRequest::findOrFail($announcement_request_id);
        // Convert some annoucement request details into the frontend format
        $announcement_request->event_datetime_human_readable =
            Carbon::createFromTimestamp($announcement_request->event_timestamp)->format('l, j F Y, g:i a');
        $announcement_request->event_datetime =
            Carbon::createFromTimestamp($announcement_request->event_timestamp)->format('m/d/Y g:i A');
        $announcement_request->media = implode(', ', $announcement_request->media()->pluck('name')->toArray());

        $announcement = Announcement
            ::where('announcement_request_id', $announcement_request_id)
            ->firstOrFail();

        // Convert some annoucement details into the frontend format
        $announcement->event_datetime =
            Carbon::createFromTimestamp($announcement->event_timestamp)->format('m/d/Y g:i A');
        // Retrieve the content of each media from the intermediate table
        $announcement->media_content = array();
        foreach ($announcement->media as $medium) {
            $announcement->media_content += array($medium->id => $medium->pivot->content);
        }
        return view('announcement.edit', [
            'media' => $media,
            'announcement_request' => $announcement_request,
            'announcement' => $announcement
        ]);
    }

    /**
     * Update an announcement into the database
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

        $now = Carbon::now();
        $announcement_id = $request->input('announcement-id');
        $revision_no = $request->input('revision-no');
        $organization_name = $request->input('organization-name');
        $title = $request->input('title');
        $content = $request->input('content');
        $duration = $request->input('duration');
        $event_datetime = $request->input('event-datetime');
        $event_timestamp = Carbon::parse($event_datetime)->timestamp;

        Announcement::where('id', $announcement_id)->update([
            'revision_no' => $revision_no,
            'organization_name' => $organization_name,
            'title' => $title,
            'content' => $content,
            'duration' => $duration,
            'editor_id' => Auth::id(),
            'event_timestamp' => $event_timestamp,
        ]);

        // Attach the chosen media and detach the unused ones
        foreach ($request->input('media') as $medium) {
            $content = $request->input('content-'.$medium);
            $media[$medium] = array('content' => $content);
        }
        $announcement = Announcement::findOrFail($announcement_id);
        $announcement->media()->sync($media);

        // Sync offline distribution
        $this->sync_offline_distribution($announcement->id);

        return redirect('/announcement/approve', 303)->with('success_message', 'Persetujuan revisi pengumuman telah berhasil.');
    }

    /**
     * Display the view announcement page
     *
     * @param Request $request
     * @param string $announcement_id
     * @return Response
     */
    public function view(Request $request, string $announcement_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $announcement = Announcement::findOrFail($announcement_id);

        // Convert some annoucement details into the frontend format
        $announcement->event_datetime_human_readable =
            Carbon::createFromTimestamp($announcement->event_timestamp)->format('l, j F Y, g:i a');
        // Retrieve the content of each media from the intermediate table
        $announcement->media_content = array();
        foreach ($announcement->media as $medium) {
            $announcement->media_content += array($medium->name => $medium->pivot->content);
        }

        return view('announcement.view', ['announcement' => $announcement]);
    }

    /**
     * Delele an announcement from the database
     *
     * @param Request $request
     * @param string $announcement_id
     * @return Response
     */
    public function delete(Request $request, string $announcement_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $announcement = Announcement::findOrFail($announcement_id);

        Announcement::destroy($announcement_id);
        return redirect('/announcement/approve', 303)
            ->with('success_message', 'Pengumuman telah berhasil dihapus.');
    }

    /**
     * Sync the offline distribution that must be linked to the announcement
     *
     * @param string $announcement_id
     * @return void
     */
    public function sync_offline_distribution(string $announcement_id)
    {
        $announcement = Announcement::findOrFail($announcement_id);
        $announcement_request = $announcement->announcement_request;
        $offline_media_ids = $announcement->media()->pluck('id')->toArray();

        $offline_distributions = OfflineDistribution
            ::where('distribution_timestamp', '>', Carbon::createFromTimestamp($announcement->event_timestamp)->subDays($announcement->duration)->timestamp)
            ->where('distribution_timestamp', '<', Carbon::createFromTimestamp($announcement->event_timestamp)->timestamp)
            ->where('deadline_timestamp', '>', $announcement_request->create_timestamp)
            ->whereIn('offline_media_id', $offline_media_ids)
            ->get();

        // Associate the announcement to the offline distribution
        $association = array();
        foreach ($offline_distributions as $distribution) {
            $content = $announcement->media()->where('id', $distribution->offline_media_id)->first()->pivot->content;
            $association += array(
                $distribution->id => ['content' => $content]
            );
        }
        $announcement->offline_distribution()->sync($association);

        return;
    }
}
