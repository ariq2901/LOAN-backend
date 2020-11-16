<?php

namespace App\Mail;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PeminjamanEmail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $borrowingId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($borrowingId)
    {
        $this->borrowingId = $borrowingId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $id = $this->borrowingId;
        $borrow = Borrowing::where("id", $id)->first();
        $userId = $borrow->user_id;
        $user = User::find($userId)->get("name")[0];
        $borrow->name = $user->name;
        return $this->from('fakeghuroba@gmail.com')
                    ->view('peminjaman')
                    ->with(
                        [
                            "borrow" => $borrow
                        ]
                        );
    }
}
