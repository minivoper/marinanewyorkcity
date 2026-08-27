<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells Marina that someone filled in the contact form.
 *
 * The reply-to is the sender's own address, so answering a lead is one click
 * rather than a copy-and-paste out of the body. The from address stays the
 * site's own: sending as the visitor would fail SPF and land the whole thing
 * in spam.
 *
 * Any attachment is deliberately linked by name rather than attached. The form
 * accepts 10 MB, uploads land on a private disk, and forwarding an unscanned
 * file straight into an inbox is not something a contact form should do on its
 * own initiative.
 */
class InquiryReceived extends Notification
{
    use Queueable;

    public function __construct(public readonly Inquiry $inquiry) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->inquiry->contacting_for.' from '.$this->inquiry->name)
            ->replyTo($this->inquiry->email, $this->inquiry->name)
            ->greeting('New contact form submission')
            ->line('Contacting for: '.$this->inquiry->contacting_for)
            ->line('Name: '.$this->inquiry->name);

        if ($this->inquiry->company) {
            $message->line('Company: '.$this->inquiry->company);
        }

        $message->line('Email: '.$this->inquiry->email);

        if ($this->inquiry->attachment_name) {
            $message->line('Attachment: '.$this->inquiry->attachment_name.' (stored at '.$this->inquiry->attachment_path.')');
        }

        return $message
            ->line('---')
            ->line($this->inquiry->message);
    }
}
