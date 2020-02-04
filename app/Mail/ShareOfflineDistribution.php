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
                if (substr($path, strpos($path, '/storage/'), strlen('/storage/')) == '/storage/') {
                    $path = substr($path, strpos($path, '/storage/') + strlen('/storage/'));
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
        return $this
            ->subject('['.config('app.name').'] '. $this->offline_distribution->name)
            ->view('offlinedistribution.share.email')
            ->with([
                'header' => $this->convert_image($this->offline_distribution->header),
                'content' => $this->convert_image($this->offline_distribution->content),
                'footer' => $this->convert_image($this->offline_distribution->footer),
            ]);
    }
}
