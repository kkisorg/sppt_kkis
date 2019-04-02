<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

class ActivateAccount extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * The token needed to activate the account.
     *
     * @var string
     */
    protected $token;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $create_time = $this->user->create_timestamp->toDateTimeString();

        return $this
            ->subject('Selamat datang di '.config('app.name'))
            ->view('user.activate.email')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'create_time' => $create_time,
                'token' => $this->token
            ]);
    }
}
