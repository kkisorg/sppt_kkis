<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Media;
use App\MonthlyOfflineDistributionSchedule;
use App\OfflineDistribution;
use App\UserActivityTracking;
use App\Http\Controllers\Traits\AnnouncementOfflineDistributionLinker;

class MonthlyOfflineDistributionScheduleController extends Controller
{
    use AnnouncementOfflineDistributionLinker;

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
     * Display the list of offline distribution
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
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_INDEX',
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

        $monthly_offline_distribution_schedules =
            MonthlyOfflineDistributionSchedule
            ::orderBy('distribution_weekofmonth')
            ->get();

        foreach ($monthly_offline_distribution_schedules as $schedule) {
            $dayofweek = array(
                0 => 'Sunday',
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday'
            );
            $schedule->media_name = $schedule->media->name;
            $schedule->distribution_dayofweek = $dayofweek[$schedule->distribution_dayofweek];
            $schedule->distribution_time = Carbon::parse($schedule->distribution_time)->format('H:i');
            $schedule->deadline_dayofweek = $dayofweek[$schedule->deadline_dayofweek];
            $schedule->deadline_time = Carbon::parse($schedule->deadline_time)->format('H:i');
        }

        return view(
            'monthlyofflinedistributionschedule.index',
            ['monthly_offline_distribution_schedules' => $monthly_offline_distribution_schedules]
        );
    }

    /**
     * Display the create monthly offline distribution schedule form
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_CREATE',
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
        // Display new distribution form
        $media = Media::has('offline_media')->where('is_active', true)->get();
        return view('monthlyofflinedistributionschedule.create', ['media' => $media]);
    }

    /**
     * Insert a new monthly offline distribution schedule into the database
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
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_CREATE',
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
        $name = $request->input('name');
        $default_header = $request->input('default-header');
        $default_footer = $request->input('default-footer');
        $media_id = $request->input('media-id');
        $distribution_weekofmonth = $request->input('distribution-weekofmonth');
        $distribution_dayofweek = $request->input('distribution-dayofweek');
        $distribution_time = $request->input('distribution-time');
        $deadline_dayofweek = $request->input('deadline-dayofweek');
        $deadline_time = $request->input('deadline-time');
        $recipient_email = $request->input('recipient-email');

        // Convert time to database format
        $distribution_time = Carbon::parse($distribution_time)->format('H:i:s');
        $deadline_time = Carbon::parse($deadline_time)->format('H:i:s');

        MonthlyOfflineDistributionSchedule::create([
            'name' => $name,
            'default_header' => $default_header,
            'default_footer' => $default_footer,
            'offline_media_id' => $media_id,
            'distribution_weekofmonth' => $distribution_weekofmonth,
            'distribution_dayofweek' => $distribution_dayofweek,
            'distribution_time' => $distribution_time,
            'deadline_dayofweek' => $deadline_dayofweek,
            'deadline_time' => $deadline_time,
            'recipient_email' => $recipient_email
        ]);

        return redirect('/monthly_offline_distribution_schedule', 303)
            ->with('success_message', 'Jadwal distribusi offline bulanan telah berhasil dibuat.');
    }

    /**
     * Display the edit monthly offline distribution schedule form
     *
     * @param Request $request
     * @param string $monthly_offline_distribution_schedule_id
     * @return Response
     */
    public function edit(Request $request, string $monthly_offline_distribution_schedule_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'DISPLAY',
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_EDIT',
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

        $monthly_offline_distribution_schedule = MonthlyOfflineDistributionSchedule::findOrFail($monthly_offline_distribution_schedule_id);

        // Convert some monthly offline distribution schedule details into the frontend format
        $monthly_offline_distribution_schedule->distribution_time =
            Carbon::parse($monthly_offline_distribution_schedule->distribution_time)->format('g:i A');
        $monthly_offline_distribution_schedule->deadline_time =
            Carbon::parse($monthly_offline_distribution_schedule->deadline_time)->format('g:i A');

        $media = Media::has('offline_media')->where('is_active', true)->get();
        return view(
            'monthlyofflinedistributionschedule.edit',
            ['monthly_offline_distribution_schedule' => $monthly_offline_distribution_schedule, 'media' => $media]
        );
    }

    /**
     * Update an monthly offline distribution schedule into the database
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
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_EDIT',
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
        $id = $request->input('id');
        $name = $request->input('name');
        $default_header = $request->input('default-header');
        $default_footer = $request->input('default-footer');
        $media_id = $request->input('media-id');
        $distribution_weekofmonth = $request->input('distribution-weekofmonth');
        $distribution_dayofweek = $request->input('distribution-dayofweek');
        $distribution_time = $request->input('distribution-time');
        $deadline_dayofweek = $request->input('deadline-dayofweek');
        $deadline_time = $request->input('deadline-time');
        $recipient_email = $request->input('recipient-email');

        // Convert time to database format
        $distribution_time = Carbon::parse($distribution_time)->format('H:i:s');
        $deadline_time = Carbon::parse($deadline_time)->format('H:i:s');

        MonthlyOfflineDistributionSchedule::where('id', $id)->update([
            'name' => $name,
            'default_header' => $default_header,
            'default_footer' => $default_footer,
            'offline_media_id' => $media_id,
            'distribution_weekofmonth' => $distribution_weekofmonth,
            'distribution_dayofweek' => $distribution_dayofweek,
            'distribution_time' => $distribution_time,
            'deadline_dayofweek' => $deadline_dayofweek,
            'deadline_time' => $deadline_time,
            'recipient_email' => $recipient_email
        ]);

        return redirect('/monthly_offline_distribution_schedule', 303)
            ->with('success_message', 'Jadwal distribusi offline bulanan telah berhasil diubah.');
    }

    /**
     * Delele an offline distribution from the database
     *
     * @param Request $request
     * @param string $monthly_offline_distribution_schedule_id
     * @return Response
     */
    public function delete(Request $request, string $monthly_offline_distribution_schedule_id)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_DELETE',
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

        MonthlyOfflineDistributionSchedule::findOrFail($monthly_offline_distribution_schedule_id);
        MonthlyOfflineDistributionSchedule::destroy($monthly_offline_distribution_schedule_id);

        return redirect('/monthly_offline_distribution_schedule', 303)
            ->with('success_message', 'Jadwal distribusi offline bulanan telah berhasil dihapus.');
    }

    /**
     * Run offline distribution insertion jobs automatically
     *
     * @return void
     */
    public function __invoke()
    {
        $timestamp = Carbon::now()->timestamp;
        $this->run($timestamp);
        return;
    }

    /**
     * Run offline distribution insertion jobs manually
     *
     * @return void
     */
    public function manual_invoke(Request $request)
    {
        // Track user activity
        UserActivityTracking::create([
            'user_id' => Auth::id(),
            'activity_type' => 'CLICK',
            'activity_details' => 'MONTHLY_OFFLINE_DISTRIBUTION_SCHEDULE_MANUAL_INVOKE',
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

        $datetime = $request->input('datetime');
        $timestamp = Carbon::parse($datetime)->timestamp;
        $this->run($timestamp);
        return redirect('/monthly_offline_distribution_schedule', 303)
            ->with('success_message', 'Jadwal distribusi offline bulanan telah berhasil dieksekusi.');
    }

    /**
     * Execute offline distribution insertion jobs
     *
     * @return void
     */
    private function run(int $timestamp)
    {
        $now = Carbon::createFromTimestamp($timestamp);
        $current_weekofmonth = $now->weekOfMonth;

        $next_month = $now->copy()->addMonth();
        $next_month_str = $next_month->format('F Y');

        // Special action for week 5
        $weekofmonths = array($current_weekofmonth);
        if ($current_weekofmonth == 5) {
            return;
        } elseif ($current_weekofmonth == 4) {
            $weekofmonths += array(5);
        }

        foreach ($weekofmonths as $weekofmonth) {
            $schedules = MonthlyOfflineDistributionSchedule
                ::where('distribution_weekofmonth', $weekofmonth)
                ->get();
            foreach ($schedules as $schedule) {
                $ordinal_number = array(
                    1 => 'first',
                    2 => 'second',
                    3 => 'third',
                    4 => 'fourth',
                    5 => 'fifth'
                );
                $dayofweek = array(
                    0 => 'Sunday',
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday'
                );

                $distribution_date = Carbon
                    ::createFromTimestamp(strtotime(
                        $ordinal_number[$weekofmonth].' '.$dayofweek[$schedule->distribution_dayofweek].' of '.$next_month_str)
                    )->format('Y-m-d');
                $distribution_time = $schedule->distribution_time;
                $distribution_timestamp = Carbon::parse($distribution_date.' '.$distribution_time)->timestamp;
                $deadline_date = Carbon
                    ::createFromTimestamp(strtotime(
                        $distribution_date.' last '.$dayofweek[$schedule->deadline_dayofweek])
                    )->format('Y-m-d');
                $deadline_time = $schedule->deadline_time;
                $deadline_timestamp = Carbon::parse($deadline_date.' '.$deadline_time)->timestamp;
                $recipient_email = $schedule->recipient_email;

                // This case happen when there is no fifth week in the following month
                if ($weekofmonth == 5) {
                    $second_week_str = 'second '.$dayofweek[$schedule->distribution_dayofweek].' of '.$next_month_str;
                    if (Carbon::parse($distribution_date)->lessThan(Carbon::createFromTimestamp(strtotime($second_week_str)))) {
                        continue;
                    }
                }

                // Avoid duplication
                $distribution_exists = OfflineDistribution
                    ::where('offline_media_id', $schedule->offline_media_id)
                    ->where('distribution_timestamp', $distribution_timestamp)
                    ->exists();
                if ($distribution_exists) {
                    continue;
                }

                $offline_distribution = OfflineDistribution::create([
                    'name' => $schedule->name.' ('.Carbon::parse($distribution_date)->format('l, j F Y').')',
                    'header' => $schedule->default_header,
                    'footer' => $schedule->default_footer,
                    'offline_media_id' => $schedule->offline_media_id,
                    'distribution_timestamp' => $distribution_timestamp,
                    'deadline_timestamp' => $deadline_timestamp,
                    'recipient_email' => $recipient_email
                ]);

                $this->sync_announcement($offline_distribution->id);
            }
        }
        return;
    }
}
