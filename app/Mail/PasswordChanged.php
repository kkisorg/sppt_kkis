<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;

class PasswordChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $update_time = $this->user->update_timestamp->toDateTimeString();

        return $this
            ->subject('['.config('app.name').'] Password Anda telah berhasil diubah')
            ->view('user.password.edit.email')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'update_time' => $update_time,
            ]);
    }
}
