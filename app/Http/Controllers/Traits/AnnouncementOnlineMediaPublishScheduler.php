<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;

use App\Announcement;
use App\AnnouncementOnlineMediaPublishSchedule;
use App\Media;

trait AnnouncementOnlineMediaPublishScheduler
{
    /**
     * Sync online media publish schedule
     *
     * @param string $announcement_id
     * @return void
     */
    public function create_online_media_publish_schedule(string $announcement_id)
    {
        $now = Carbon::now();

        $announcement = Announcement::findOrFail($announcement_id);

        $online_media_ids = $announcement->media()->pluck('id')->toArray();
        $online_media = Media
            ::whereHas('online_media')
            ->whereIn('id', $online_media_ids)
            ->where('is_active', True)
            ->get();

        // Define the publish time threshold.
        $first_publish_time = Carbon
            ::createFromTimestamp($announcement->event_timestamp)
            ->subDays($announcement->duration);
        $last_publish_time = Carbon
            ::createFromTimestamp($announcement->event_timestamp)
            ->subDays(3);

        // Sanity check, first <= last
        if ($last_publish_time->isBefore($first_publish_time)) {
            return;
        }

        $publish_times = array();
        // If the first publish time has been passed, then publish immediately.
        // Otherwise, wait for the first publish time.
        if ($first_publish_time->isBefore($now)) {
            // Only allow publish 15 minutes after the approval.
            $publish_time = $now->copy()->addMinutes(15);
            $adjusted_publish_time = $publish_time->copy();
        } else {
            // Publish at 8 am instead.
            $publish_time = $first_publish_time->copy();
            $adjusted_publish_time = Carbon::create(
                $publish_time->year, $publish_time->month, $publish_time->day,
                8, 0, 0);
        }
        if ($publish_time->isBefore($last_publish_time)) {
            array_push($publish_times, $adjusted_publish_time);
        } else {
            return;
        }

        // Find the subsequent publish time. The goal is to publish once a month.
        while (True) {
            $publish_time = $publish_time->copy()->addMonth();
            if ($publish_time->isBefore($last_publish_time)) {
                $adjusted_publish_time = Carbon::create(
                    $publish_time->year, $publish_time->month, $publish_time->day,
                    8, 0, 0);
                array_push($publish_times, $adjusted_publish_time);
            } else {
                break;
            }
        }

        // Create or update the publish schedule in the online media.
        foreach ($online_media as $medium) {
            $title = $announcement->title;
            // Get the content of the annoucement specific to the medium.
            $content = $announcement->media()->where('id', $medium->id)->first()->pivot->content;

            // Find sequence number, the non-initial announcement will not be updated.
            $published_count = AnnouncementOnlineMediaPublishSchedule
                ::where('announcement_id', $announcement_id)
                ->where('online_media_id', $medium->id)
                ->where('status', '!=', 'INITIAL')
                ->max('sequence');
            if ($published_count === null) {
                $next_published_sequence = 1;
            } else {
                $next_published_sequence = $published_count + 1;
            }

            // Insert new publish schedule record if not exists.
            // Otherwise, update the record.
            $publish_pointer = 0;
            $sequence_pointer = $next_published_sequence;
            while (True) {
                if (($publish_pointer > (sizeof($publish_times) - 1)) | ($sequence_pointer > 3)) {
                    break;
                }

                AnnouncementOnlineMediaPublishSchedule::updateOrCreate([
                    'announcement_id' => $announcement_id,
                    'online_media_id' => $medium->id,
                    'sequence' => $sequence_pointer
                ], [
                    'title' => $title,
                    'content' => $content,
                    'status' => 'INITIAL',
                    'publish_timestamp' => $publish_times[$publish_pointer]->timestamp
                ]);

                $publish_pointer = $publish_pointer + 1;
                $sequence_pointer = $sequence_pointer + 1;
            }

            // Delete unused schedule if exists
            AnnouncementOnlineMediaPublishSchedule
                ::where('announcement_id', $announcement_id)
                ->where('online_media_id', $medium->id)
                ->where('sequence', '>=', $sequence_pointer)
                ->delete();
        }

        return;
    }
}
