<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuratPeringatan extends Mailable
{
    use Queueable, SerializesModels;

    protected $userId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $id = $this->userId;
        $user = User::where("id", $id)->first();
        return $this->from('fakeghuroba@gmail.com')
                    ->view('suratperingatan')
                    ->with(
                        [
                            "user" => $user
                        ]
                    );
    }
}
