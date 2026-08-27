<?php

namespace App\Models;

use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A submission from the contact form.
 *
 * Until this existed, `ContactController::store()` validated the request,
 * flashed a thank-you, and threw the message away. Every partnership enquiry,
 * press request and event invitation since the site launched went nowhere.
 *
 * The row is written before the notification is sent, and `notified_at` records
 * whether the mail actually went out — a lead that arrives while the mail
 * provider is down is still a lead.
 */
#[Fillable([
    'contacting_for',
    'name',
    'company',
    'email',
    'message',
    'attachment_path',
    'attachment_name',
    'ip_address',
    'user_agent',
])]
class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
        ];
    }
}
