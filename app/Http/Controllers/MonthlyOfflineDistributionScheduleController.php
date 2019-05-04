<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Media;
use App\MonthlyOfflineDistributionSchedule;
use App\OfflineDistribution;

class MonthlyOfflineDistributionScheduleController extends Controller
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
     * Display the list of offline distribution
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
            'deadline_time' => $deadline_time
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
            'deadline_time' => $deadline_time
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
}
