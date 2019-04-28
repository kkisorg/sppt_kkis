<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Media;
use App\OfflineDistribution;

class OfflineDistributionController extends Controller
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

        $now = Carbon::now();
        $two_weeks_ago = $now->subDays(14);
        $two_weeks_ago_timestamp = $two_weeks_ago->timestamp;

        // Show ongoing and past (last two weeks only) offline distribution
        $offline_distributions = OfflineDistribution
            ::where('distribution_timestamp', '>', $two_weeks_ago_timestamp)
            ->orderBy('distribution_timestamp')
            ->get();

        foreach ($offline_distributions as $distribution) {
            $distribution->distribution_datetime =
                Carbon::createFromTimestamp($distribution->distribution_timestamp)->format('l, j F Y, g:i a');
            $distribution->deadline_datetime =
                Carbon::createFromTimestamp($distribution->deadline_timestamp)->format('l, j F Y, g:i a');
            $distribution->media_name = $distribution->media->name;
            $now_to_deadline_diff =
                Carbon::now()->diffInSeconds(Carbon::createFromTimestamp($distribution->deadline_timestamp), false);
            if ($now_to_deadline_diff < 0) {
                $distribution->status = 'FINAL';
            } elseif (0 < $now_to_deadline_diff && $now_to_deadline_diff <= 24 * 3600) {
                $distribution->status = 'MENDEKATI BATAS AKHIR (DEADLINE)';
            } else {
                $distribution->status = 'MENERIMA PENGUMUMAN';
            }
        }
        return view('offlinedistribution.index', ['offline_distributions' => $offline_distributions]);
    }

    /**
     * Display the create offline distribution form
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
        return view('offlinedistribution.create', ['media' => $media]);
    }

    /**
     * Insert a new offline distribution into the database
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
        $header = $request->input('header');
        $footer = $request->input('footer');
        $content = $request->input('content');
        $media_id = $request->input('media-id');
        $distribution_datetime = $request->input('distribution-datetime');
        $deadline_datetime = $request->input('deadline-datetime');
        // Convert dates to database format
        $distribution_timestamp = Carbon::parse($distribution_datetime)->timestamp;
        $deadline_timestamp = Carbon::parse($deadline_datetime)->timestamp;
        OfflineDistribution::create([
            'name' => $name,
            'header' => $header,
            'footer' => $footer,
            'content' => $content,
            'offline_media_id' => $media_id,
            'distribution_timestamp' => $distribution_timestamp,
            'deadline_timestamp' => $deadline_timestamp
        ]);
        return redirect('/offline_distribution', 303)
            ->with('success_message', 'Distribusi telah berhasil dibuat.');
    }

    /**
     * Display the edit offline distribution form
     *
     * @param Request $request
     * @param string $offline_distribution_id
     * @return Response
     */
    public function edit(Request $request, string $offline_distribution_id)
    {
        $offline_distribution = OfflineDistribution::findOrFail($offline_distribution_id);
        // Convert some annoucement request details into the frontend format
        $offline_distribution->distribution_datetime =
            Carbon::createFromTimestamp($offline_distribution->distribution_timestamp)->format('m/d/Y g:i A');
        $offline_distribution->deadline_datetime =
            Carbon::createFromTimestamp($offline_distribution->deadline_timestamp)->format('m/d/Y g:i A');

        $media = Media::has('offline_media')->where('is_active', true)->get();
        return view(
            'offlinedistribution.edit',
            ['offline_distribution' => $offline_distribution, 'media' => $media]
        );
    }

    /**
     * Update an offline distribution into the database
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
        $offline_distribution_id = $request->input('id');
        $name = $request->input('name');
        $header = $request->input('header');
        $footer = $request->input('footer');
        $content = $request->input('content');
        $media_id = $request->input('media-id');
        $distribution_datetime = $request->input('distribution-datetime');
        $deadline_datetime = $request->input('deadline-datetime');
        // Convert dates to database format
        $distribution_timestamp = Carbon::parse($distribution_datetime)->timestamp;
        $deadline_timestamp = Carbon::parse($deadline_datetime)->timestamp;
        OfflineDistribution::where('id', $offline_distribution_id)->update([
            'name' => $name,
            'header' => $header,
            'footer' => $footer,
            'content' => $content,
            'offline_media_id' => $media_id,
            'distribution_timestamp' => $distribution_timestamp,
            'deadline_timestamp' => $deadline_timestamp
        ]);
        return redirect('/offline_distribution', 303)
            ->with('success_message', 'Distribusi telah berhasil diubah.');
    }

    /**
     * Display the view offline distribution page
     *
     * @param Request $request
     * @param string $offline_distribution_id
     * @return Response
     */
    public function view(Request $request, string $offline_distribution_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $offline_distribution = OfflineDistribution::findOrFail($offline_distribution_id);

        // Convert some offline distribution details into the frontend format
        $offline_distribution->media_name = $offline_distribution->media->name;
        $offline_distribution->distribution_datetime =
            Carbon::createFromTimestamp($offline_distribution->distribution_timestamp)->format('l, j F Y, g:i a');
        $offline_distribution->deadline_datetime =
            Carbon::createFromTimestamp($offline_distribution->deadline_timestamp)->format('l, j F Y, g:i a');

        return view('offlinedistribution.view', ['offline_distribution' => $offline_distribution]);
    }

    /**
     * Delele an offline distribution from the database
     *
     * @param Request $request
     * @param string $offline_distribution_id
     * @return Response
     */
    public function delete(Request $request, string $offline_distribution_id)
    {
        // Non-admin cannot perform this action
        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403);
        }

        $offline_distribution = OfflineDistribution::findOrFail($offline_distribution_id);

        OfflineDistribution::destroy($offline_distribution_id);
        return redirect('/offline_distribution', 303)
            ->with('success_message', 'Distribusi telah berhasil dihapus.');
    }
}
