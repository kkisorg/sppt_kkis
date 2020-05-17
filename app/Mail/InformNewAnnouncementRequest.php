<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class InformNewAnnouncementRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The array of new announcement request titles.
     *
     * @var array
     */
    protected $new_announcement_request_titles;

    /**
     * The array of revised announcement request titles.
     *
     * @var array
     */
    protected $revised_announcement_request_titles;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $new_announcement_request_titles, array $revised_announcement_request_titles)
    {
        $this->new_announcement_request_titles = $new_announcement_request_titles;
        $this->revised_announcement_request_titles = $revised_announcement_request_titles;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('['.config('app.name').'] Permintaan Pengumuman Baru')
            ->view('announcement.announcement_request.new.email')
            ->with([
                'new_announcement_request_titles' => $this->new_announcement_request_titles,
                'revised_announcement_request_titles' => $this->revised_announcement_request_titles,
            ]);
    }
}
