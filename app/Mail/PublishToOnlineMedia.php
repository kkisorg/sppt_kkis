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
     * The flag indicating whether or not to mention app name in the email's subject.
     *
     * @var bool
     */
    protected $mention_app_name_in_subject;

    /**
     * The flag indicating whether or not to mention media name in the email's subject.
     *
     * @var bool
     */
    protected $mention_media_name_in_subject;

    /**
     * The flag indicating whether or not to mention app name in the email's body.
     *
     * @var bool
     */
    protected $mention_app_name_in_body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(AnnouncementOnlineMediaPublishSchedule $publish_schedule,
                                bool $mention_media_name_in_subject = true,
                                bool $mention_app_name_in_subject = true,
                                bool $mention_app_name_in_body = true)
    {
        $this->publish_schedule = $publish_schedule;
        $this->mention_app_name_in_subject = $mention_app_name_in_subject;
        $this->mention_media_name_in_subject = $mention_media_name_in_subject;
        $this->mention_app_name_in_body = $mention_app_name_in_body;

    }

    /**
     * Convert image path so that it is accessible by public.
     * Disclaimer: This is a hacky way.
     *
     * @param string $str
     * @return string
     */
    public function convert_image_path(string $str)
    {
        return str_replace('<img src="', '<img src="'.URL::to('/'), $str);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $media_name = $this->publish_schedule->media->name;

        $subject = '';
        if ($this->mention_app_name_in_subject) {
            $subject .= '['.config('app.name').'] ';
        }
        if ($this->mention_media_name_in_subject) {
            $subject .= '['.$media_name.'] ';
        }
        $subject .= $this->publish_schedule->title;

        return $this
            ->subject($subject)
            ->view('announcement.distribution_schedule.publish.email')
            ->with([
                'content' => $this->convert_image_path($this->publish_schedule->content),
                'mention_app_name_in_body' => $this->mention_app_name_in_body
            ]);
    }
}
