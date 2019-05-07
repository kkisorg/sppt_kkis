<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;

use App\Announcement;
use App\OfflineDistribution;

trait AnnouncementOfflineDistributionLinker
{
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

    /**
     * Sync the announcement that must be linked to the offline distribution
     *
     * @param string $offline_distribution_id
     * @return void
     */
    public function sync_announcement(string $offline_distribution_id)
    {
        $offline_distribution = OfflineDistribution::findOrFail($offline_distribution_id);

        $announcements = Announcement::whereRaw(
            'event_timestamp between ? and (? + duration * 24 * 3600)',
            [$offline_distribution->distribution_timestamp, $offline_distribution->distribution_timestamp]
        )->whereHas(
            'announcement_request', function ($query) use ($offline_distribution) {
                $query->where('create_timestamp', '<', $offline_distribution->deadline_timestamp);
            }
        )->whereHas(
            'media', function ($query) use ($offline_distribution) {
                $query->where('id', '=', $offline_distribution->offline_media_id);
            }
        )->get();

        // Associate the announcement to the offline distribution
        $association = array();
        foreach ($announcements as $announcement) {
            $content = $announcement->media()->where('id', $offline_distribution->offline_media_id)->first()->pivot->content;
            $association += array(
                $announcement->id => ['content' => $content]
            );
        }
        $offline_distribution->announcement()->sync($association);

        return;
    }
}
