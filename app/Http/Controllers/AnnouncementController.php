<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Announcement;
use App\AnnouncementOnlineMediaPublishSchedule;
use App\AnnouncementRequest;
use App\Media;
use App\OfflineDistribution;
use App\UserActivityTracking;
use App\Http\Controllers\Traits\AnnouncementOfflineDistributionLinker;
use App\Http\Controllers\Traits\AnnouncementOnlineMediaPublishScheduler;

class AnnouncementController extends Controller
{
    use AnnouncementOfflineDistributionLinker, AnnouncementOnlineMediaPublishScheduler;

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
     * Display the announcement approval center page
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_INDEX',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

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

        return view('announcement.index', [
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
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_CREATE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

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
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'ANNOUNCEMENT_CREATE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

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
        if ($request->has('media')) {
            foreach ($request->input('media') as $medium) {
                $content = $request->input('content-'.$medium);
                $media[$medium] = array('content' => $content);
            }
            $announcement->media()->attach($media);
        }

        // Sync offline distribution
        $this->sync_offline_distribution($announcement->id);

        // Create online media publish schedule
        $this->create_online_media_publish_schedule($announcement->id);

        return redirect('/announcement/edit_distribution_schedule/'.$announcement->id, 303)
            ->with('success_message', 'Persetujuan pengumuman baru telah berhasil. '.
                'Silakan periksa jadwal edar pengumuman ini dan lakukan perubahan jika diperlukan.');
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
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_EDIT',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

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
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'ANNOUNCEMENT_EDIT',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

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

        $announcement = Announcement::findOrFail($announcement_id);

        // Attach the chosen media and detach the unused ones
        if ($request->has('media')) {
            foreach ($request->input('media') as $medium) {
                $content = $request->input('content-'.$medium);
                $media[$medium] = array('content' => $content);
            }
            $announcement->media()->sync($media);
        } else {
            $announcement->media()->detach();
        }

        // Sync offline distribution
        $this->sync_offline_distribution($announcement->id);

        // Create online media publish schedule
        $this->create_online_media_publish_schedule($announcement->id);

        return redirect('/announcement/edit_distribution_schedule/'.$announcement_id, 303)
            ->with('success_message', 'Persetujuan revisi pengumuman telah berhasil. '.
                'Silakan periksa jadwal edar pengumuman ini dan lakukan perubahan jika diperlukan.');
    }

    /**
     * Display the configure announcement form
     * The configuration is about the link to offline distribution
     * and the online media publish schedule
     *
     * @param Request $request
     * @param string $announcement_id
     * @return Response
     */
    public function edit_distribution_schedule(Request $request, string $announcement_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_DISTRIBUTION_SCHEDULE_EDIT',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        $now = Carbon::now();

        $announcement = Announcement::findOrFail($announcement_id);

        // List all the linked offline distribution
        $announcement->offline_distribution_ids = $announcement
            ->offline_distribution()->pluck('id')->toArray();
        // List all offline distribution which media is ticked
        $announcement_media_ids = $announcement
            ->media()->pluck('id')->toArray();
        $present_offline_distributions = OfflineDistribution
            ::where('distribution_timestamp', '>', $now->timestamp)
            ->whereIn('offline_media_id', $announcement_media_ids)
            ->orderBy('distribution_timestamp')
            ->get();

        $online_media_ids = $announcement->media()->pluck('id')->toArray();
        $online_media = Media
            ::whereHas('online_media')
            ->whereIn('id', $online_media_ids)
            ->where('is_active', True)
            ->get();

        // Populate the online media distribution schedule
        $online_media_publish_schedules_array = array();
        foreach($online_media as $medium) {
            $online_media_publish_schedules_array[$medium->id] =
                array(1 => '', 2 => '', 3 => '');
        }
        foreach ($announcement->announcement_online_media_publish_schedule()->get() as $schedule) {
            $online_media_publish_schedules_array[$schedule->online_media_id][$schedule->sequence] =
                Carbon::createFromTimestamp($schedule->publish_timestamp)->format('m/d/Y g:i A');
        }
        $announcement->online_media_publish_schedules = $online_media_publish_schedules_array;

        return view('announcement.distribution_schedule.edit', [
            'announcement' => $announcement,
            'present_offline_distributions' => $present_offline_distributions,
            'online_media' => $online_media
        ]);
    }

    /**
     * Update the publish configuration of an announcement into the database
     *
     * @param Request $request
     * @param string $announcement_id
     * @return Response
     */
    public function update_distribution_schedule(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'ANNOUNCEMENT_DISTRIBUTION_SCHEDULE_EDIT',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        $now = Carbon::now();

        $announcement_id = $request->input('announcement-id');
        $announcement = Announcement::findOrFail($announcement_id);

        $online_media_ids = $announcement->media()->pluck('id')->toArray();
        $online_media = Media
            ::whereHas('online_media')
            ->whereIn('id', $online_media_ids)
            ->where('is_active', True)
            ->get();

        // Update (associate) which offline distribution should be linked
        // to the announcement.

        // Method: Detach first then re-attach
        $present_offline_distribution_ids = OfflineDistribution
            ::where('distribution_timestamp', '>', $now->timestamp)
            ->pluck('id')->toArray();
        $announcement->offline_distribution()->detach($present_offline_distribution_ids);

        $offline_distribution_ids = $request->input('offline-distribution');
        if ($offline_distribution_ids !== null) {
            $offline_distributions = OfflineDistribution
                ::whereIn('id', $offline_distribution_ids)
                ->get();

            $offline_distribution_association = array();
            foreach ($offline_distributions as $distribution) {
                $content = $announcement->media()->where('id', $distribution->offline_media_id)->first()->pivot->content;
                $offline_distribution_association += array(
                    $distribution->id => ['content' => $content]
                );
            }
            $announcement->offline_distribution()->syncWithoutDetaching($offline_distribution_association);
        }

        // Update the online media publish schedule
        // which were set automatically by system.
        foreach ($online_media as $medium) {
            $title = $announcement->title;
            // Get the content of the annoucement specific to the medium.
            $content = $announcement->media()->where('id', $medium->id)->first()->pivot->content;
            for ($sequence = 1; $sequence <= 3; $sequence++) {
                if ($request->filled('online-publish-datetime-'.$medium->id.'-'.$sequence)) {
                    // Update or create publish schedule
                    $publish_datetime = $request->input('online-publish-datetime-'.$medium->id.'-'.$sequence);
                    $publish_timestamp = Carbon::parse($publish_datetime)->timestamp;

                    AnnouncementOnlineMediaPublishSchedule::updateOrCreate([
                        'announcement_id' => $announcement_id,
                        'online_media_id' => $medium->id,
                        'sequence' => $sequence
                    ], [
                        'title' => $title,
                        'content' => $content,
                        'status' => 'INITIAL',
                        'publish_timestamp' => $publish_timestamp
                    ]);
                } else {
                    // Delete unused schedule if exists
                    AnnouncementOnlineMediaPublishSchedule
                        ::where('announcement_id', $announcement_id)
                        ->where('online_media_id', $medium->id)
                        ->where('sequence', $sequence)
                        ->where('status', 'INITIAL')
                        ->delete();
                }
            }
        }

        return redirect('/announcement', 303)
            ->with('success_message', 'Jadwal edar pengumuman telah diatur/diubah.');
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
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_VIEW',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

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
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'ANNOUNCEMENT_DELETE',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $announcement = Announcement::findOrFail($announcement_id);

        Announcement::destroy($announcement_id);
        return redirect('/announcement', 303)
            ->with('success_message', 'Pengumuman telah berhasil dihapus.');
    }

    /**
     * Display the list of announcement
     *
     * @param Request $request
     * @return Response
     */
    public function view_all(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'ANNOUNCEMENT_VIEW_ALL',
            'full_url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'is_secure' => $request->secure(),
            'ip' => $request->ip(),
            'header' => json_encode($request->header()),
        ]);

        $now = Carbon::now();
        $current_timestamp = $now->timestamp;

        $present_announcements = Announcement
            ::where('event_timestamp', '>', $current_timestamp)
            ->orderBy('event_timestamp')
            ->get();

        return view('announcement.public.view', ['present_announcements' => $present_announcements]);
    }
}
