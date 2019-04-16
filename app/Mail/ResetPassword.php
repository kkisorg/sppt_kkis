<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

class ResetPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * The token needed to reset the password.
     *
     * @var string
     */
    protected $token;

    /**
     * The time when the reset password was requested
     *
     * @var string
     */
     protected $create_time;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token, string $create_time)
    {
        $this->user = $user;
        $this->token = $token;
        $this->create_time = $create_time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('['.config('app.name').'] Permintaan untuk atur ulang (reset) password')
            ->view('user.password.reset.email')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'create_time' => $this->create_time,
                'token' => $this->token
            ]);
    }
}
