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
     * Encode image into base64
     *
     * @param string $str
     * @return string
     */
    public function convert_image(string $str)
    {
        return preg_replace_callback(
            '/(<img src=")(.*?)(")/',
            function ($matches) {
                $path = $matches[2];
                $file_extension = pathinfo($path, PATHINFO_EXTENSION);

                $supported_extension = array('tiff', 'tif', 'jpeg', 'jpg', 'gif', 'png');
                if (!in_array($file_extension, $supported_extension)) {
                    return $matches[1].$matches[2].$matches[3];
                }

                // According to the documentation, the URL is retrieved by
                // prepending "/storage" to the given path; so to reverse it,
                // "/storage" is trimmed.
                // Documentation: https://laravel.com/docs/5.6/filesystem#file-urls
                if (substr($path, 0, strlen('/storage/')) == '/storage/') {
                    $path = substr($path, strlen('/storage/'));
                }
                $data = file_get_contents(storage_path('app/public/'.$path));
                $base64 = 'data:image/'.$file_extension.';base64,'.base64_encode($data);
                return $matches[1].$base64.$matches[3];
            },
            $str
        );
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
                'content' => $this->convert_image($this->publish_schedule->content),
                'mention_app_name_in_body' => $this->mention_app_name_in_body
            ]);
    }
}
