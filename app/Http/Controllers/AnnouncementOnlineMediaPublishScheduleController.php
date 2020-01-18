<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use App\Announcement;
use App\AnnouncementOnlineMediaPublishSchedule;
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
     * Run online media publication jobs automatically.
     *
     * Publish schedule with status = 'INITIAL' will be attempted immediately.
     * Publish schedule with status = 'FAILED' will be reattempted every hour.
     *
     * @return void
     */
    public function run() {
        $now = Carbon::now();
        $current_timestamp = $now->timestamp;

        $schedules = AnnouncementOnlineMediaPublishSchedule
            ::where('publish_timestamp', '<=', $current_timestamp);
        if ($now->format('i') !== '00') {
            // First attempt only.
            $schedules = $schedules->where('status', 'INITIAL');
        } else {
            // First attempt and re-attempt of the failed ones.
            $schedules = $schedules->whereIn('status', ['INITIAL', 'FAILED']);
        }
        $schedules = $schedules->get();
        foreach ($schedules as $schedule) {
            // First, mark as ongoing
            $schedule->update(['status' => 'ON_PROGRESS']);

            $media = $schedule->media;
            $media_name = $media->name;
            $media_name_lower = strtolower($media_name);

            try {
                switch ($media_name_lower) {
                    case 'website':
                        // Send email to the website mailbox.
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

                        Mail::to($admin_email_array)
                            ->bcc(config('mail.from.address'))
                            ->send(new PublishToOnlineMedia($schedule));

                }
            } catch (Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                continue;
            } catch (\Exception $e) {
                // If not success, mark as failed.
                $schedule->update(['status' => 'FAILED']);
                continue;
            }

            // Finally, mark as success.
            $schedule->update(['status' => 'SUCCESS']);
        }
    }
}
