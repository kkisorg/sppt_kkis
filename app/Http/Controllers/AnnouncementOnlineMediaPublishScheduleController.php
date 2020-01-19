<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use App\Announcement;
use App\AnnouncementOnlineMediaPublishSchedule;
use App\AnnouncementOnlineMediaPublishRecord;
use App\Media;
use App\User;
use App\Mail\PublishToOnlineMedia;

class AnnouncementOnlineMediaPublishScheduleController extends Controller
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
     * Display the list of announcement online media publish schedule
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
        $schedules = AnnouncementOnlineMediaPublishSchedule::orderBy('publish_timestamp', 'desc')->get();
        foreach ($schedules as $schedule) {
            $schedule->publish_datetime =
                Carbon::createFromTimestamp($schedule->publish_timestamp)->format('l, j F Y, g:i a');
        }
        return view('announcementonlinemediapublishschedule.index', ['announcement_online_media_publish_schedules' => $schedules]);
    }

    /**
     * Display the view publish schedule page
     *
     * @param Request $request
     * @param string $schedule_id
     * @return Response
     */
    public function view(Request $request, string $schedule_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $schedule = AnnouncementOnlineMediaPublishSchedule::findOrFail($schedule_id);

        // Convert some schedule details into the frontend format
        $schedule->publish_datetime =
            Carbon::createFromTimestamp($schedule->publish_timestamp)->format('l, j F Y, g:i a');

        // Retrieve the records
        $records = $schedule
            ->announcement_online_media_publish_record()
            ->orderBy('create_timestamp')
            ->get();
        foreach ($records as $record) {
            $record->request_parameter =
                json_encode(json_decode($record->request_parameter), JSON_PRETTY_PRINT);
            $record->response_content =
                json_encode(json_decode($record->response_content), JSON_PRETTY_PRINT);
            $record->create_datetime = $record->create_timestamp->format('l, j F Y, g:i a');
        }

        return view('announcementonlinemediapublishschedule.view', [
            'schedule' => $schedule,
            'records' => $records
        ]);
    }

    /**
     * Run publish jobs automatically.
     *
     * Publish schedule with status = 'INITIAL' will be attempted immediately.
     * Publish schedule with status = 'FAILED' will be reattempted every hour.
     *
     * @return void
     */
    public function __invoke()
    {
        $now = Carbon::now();
        $current_timestamp = $now->timestamp;

        $schedules = AnnouncementOnlineMediaPublishSchedule
            ::where('publish_timestamp', '<=', $current_timestamp);
        if ($now->format('i') !== '00') {
            // First attempt only.
            $schedules = $schedules->where('status', 'INITIAL');
        } else {
            // First attempt and re-attempt of the failed ones.
            $schedules = $schedules->where('status', '!=', 'SUCCESS');
        }
        $schedule_ids = $schedules->pluck('id')->toArray();
        $this->run($schedule_ids);
        return;
    }

    /**
     * Run publish jobs manually.
     *
     * @return void
     */
    public function manual_invoke(Request $request, string $schedule_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $this->run(array($schedule_id), true);
        return back(303)->with('success_message', 'Task publikasi ke media online telah berhasil dieksekusi.');
    }

    /**
     * Run online media publication jobs.
     *
     * @return void
     */
    private function run(array $announcement_online_media_publish_schedule_id, bool $is_manual = false)
    {
        $schedules = AnnouncementOnlineMediaPublishSchedule
            ::whereIn('id', $announcement_online_media_publish_schedule_id)
            ->get();

        foreach ($schedules as $schedule) {
            // First, mark as ongoing
            $schedule->update(['status' => 'ON_PROGRESS']);

            $media = $schedule->media;
            $media_name = $media->name;
            $media_name_lower = strtolower($media_name);

            $record = AnnouncementOnlineMediaPublishRecord::create([
                'announcement_online_media_publish_schedule_id' => $schedule->id,
                'request_parameter' => '',
                'is_manual' => $is_manual,
                'creator_id' => $is_manual ? Auth::id() : null
            ]);

            try {
                switch ($media_name_lower) {
                    case 'website':
                        // Send email to the website mailbox.

                        $request_parameter = array(
                            'announcement_online_media_publish_schedule_id' => $schedule->id,
                            'announcement_online_media_publish_schedule' => $schedule->toJson(),
                            'mention_media_name_in_subject' => false,
                            'mention_app_name_in_subject' => false,
                            'mention_app_name_in_body' => false,
                            'to' => array(env('WEBSITE_MAILBOX_EMAIL')),
                            'bcc' => array(config('mail.from.address'))
                        );
                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to(env('WEBSITE_MAILBOX_EMAIL'))
                            ->bcc(config('mail.from.address'))
                            ->send(new PublishToOnlineMedia(
                                $schedule, false, false, false
                            ));
                        break;
                    default:
                        // Send email to administrator.
                        $admin_email_array = User::where('is_admin', true)
                                                ->pluck('email')->toArray();

                        $request_parameter = array(
                            'announcement_online_media_publish_schedule_id' => $schedule->id,
                            'announcement_online_media_publish_schedule' => $schedule->toJson(),
                            'mention_media_name_in_subject' => true,
                            'mention_app_name_in_subject' => true,
                            'mention_app_name_in_body' => true,
                            'to' => $admin_email_array,
                            'bcc' => array(config('mail.from.address'))
                        );
                        $record->update(['request_parameter' => json_encode($request_parameter)]);

                        Mail::to($admin_email_array)
                            ->bcc(config('mail.from.address'))
                            ->send(new PublishToOnlineMedia($schedule));

                }
            } catch (Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => $e->getMessage().'\n'.$e->getTraceAsString()
                ]);
                continue;
            } catch (\Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                $record->update([
                    'status' => 'FAILED',
                    'error' => $e->getMessage().'\n'.$e->getTraceAsString()
                ]);
                continue;
            }

            // Finally, mark as success.
            $schedule->update(['status' => 'SUCCESS']);
            $record->update(['status' => 'SUCCESS']);
        }
    }
}
