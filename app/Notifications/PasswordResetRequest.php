<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetRequest extends Notification
{
    use Queueable;
    protected $token;
    public $passwordresets;
    /**
    * Create a new notification instance.
    *
    * @return void
    */
   

     public function __construct($token)
    {
        $this->token = $token;
    }
    /**
    * Get the notification's delivery channels.
    *
    * @param  mixed  $notifiable
    * @return array
    */
    public function via($notifiable)
    {
        return ['mail'];
    }
     /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */


     public function toMail($notifiable)
     {
        $url = $this->token;
        return (new MailMessage)
            ->subject('Password Reset')
            ->greeting('Hello')
            ->line('You are receiving this email because we received a password reset request for your account.',$url)
            ->line('Your secret OTP is')
            ->line($url)
            ->line('If you did not request a password reset, no further action is required.');
    }
    /**
    * Get the array representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return array
    */

  
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
