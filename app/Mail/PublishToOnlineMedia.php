<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

use App\AnnouncementOnlineMediaPublishSchedule;

class PublishToOnlineMedia extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The AnnouncementOnlineMediaPublishSchedule instance.
     *
     * @var AnnouncementOnlineMediaPublishSchedule
     */
    protected $publish_schedule;

   /**
    * The array of image_path.
    *
    * @var array
    */
    protected $image_path_array;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AnnouncementOnlineMediaPublishSchedule $publish_schedule)
    {
        $this->publish_schedule = $publish_schedule;
        $this->image_path_array = array();

    }

    /**
     * Extract image URL to an array and then remove the <img> tag.
     *
     * @param string $str
     * @return string
     */
    public function extract_and_remove_image_path(string $str)
    {
        // Extract all image URL.
        preg_match_all('/(<img src=")(.*?)(")/', $str, $tmp_image_path_array);
        foreach ($tmp_image_path_array[2] as $image_path) {
            if (substr($image_path, strpos($image_path, '/storage/'), strlen('/storage/')) == '/storage/') {
                $image_path = substr($image_path, strpos($image_path, '/storage/') + strlen('/storage/'));
            }
            array_push($this->image_path_array, storage_path('app/public/'.$image_path));
        }

        // Delete img tag from the string.
        return preg_replace('/<figure class="image"><img[^>]+?\><\/figure>/i', '', $str);;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $media_name = $this->publish_schedule->media->name;

        // Extract subject.
        $subject = '['.config('app.name').'] ';
        $subject .= '['.$media_name.'] ';
        $subject .= $this->publish_schedule->title;

        // Extract attachment (URL to attachments) and remove URL from content.
        $content = $this->extract_and_remove_image_path($this->publish_schedule->content);

        $email = $this
            ->subject($subject)
            ->view('announcement.distribution_schedule.publish.default.email')
            ->with(['content' => $content]);

        foreach ($this->image_path_array as $image_path) {
            $email->attach($image_path);
        }

        return $email;
    }
}
