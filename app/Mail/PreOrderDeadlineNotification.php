<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreOrderDeadlineNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $mealType;
    public $minutesLeft;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mealType, $minutesLeft)
    {
        $this->mealType = $mealType;
        $this->minutesLeft = $minutesLeft;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Pre-order Deadline Reminder: {$this->mealType}")
            ->view('emails.pre-order-deadline')
            ->with([
                'mealType' => $this->mealType,
                'minutesLeft' => $this->minutesLeft
            ]);
    }
} 