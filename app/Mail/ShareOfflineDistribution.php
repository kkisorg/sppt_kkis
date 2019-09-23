<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

use App\OfflineDistribution;

class ShareOfflineDistribution extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The OfflineDistribution instance.
     *
     * @var OfflineDistribution
     */
    protected $offline_distribution;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(OfflineDistribution $offline_distribution)
    {
        $this->offline_distribution = $offline_distribution;
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
        return $this
            ->subject('['.config('app.name').'] '. $this->offline_distribution->name)
            ->view('offlinedistribution.share.email')
            ->with([
                'header' => $this->convert_image_path($this->offline_distribution->header),
                'content' => $this->convert_image_path($this->offline_distribution->content),
                'footer' => $this->convert_image_path($this->offline_distribution->footer),
            ]);
    }
}
