<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Notifications\InquiryReceived;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The contact form used to validate a submission, flash a thank-you and throw
 * the message away. Every one of these tests exists because a lead was lost.
 */
class InquiryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_submission_is_stored_and_mailed(): void
    {
        Notification::fake();

        $this->post('/contact', [
            'contacting_for' => 'SM Partnership Inquiry',
            'name' => 'Alex Rivera',
            'company' => 'Rivera Studio',
            'email' => 'alex@example.com',
            'message' => 'We would like to talk about a campaign.',
        ])
            ->assertRedirect(route('contact.show'))
            ->assertSessionHas('status');

        $inquiry = Inquiry::query()->sole();

        $this->assertSame('SM Partnership Inquiry', $inquiry->contacting_for);
        $this->assertSame('Alex Rivera', $inquiry->name);
        $this->assertSame('Rivera Studio', $inquiry->company);
        $this->assertSame('alex@example.com', $inquiry->email);
        $this->assertSame('We would like to talk about a campaign.', $inquiry->message);
        $this->assertNotNull($inquiry->notified_at);

        Notification::assertSentOnDemand(
            InquiryReceived::class,
            fn (InquiryReceived $notification, array $channels, object $notifiable): bool => $notification->inquiry->is($inquiry)
                && in_array(config('site.email'), $notifiable->routes['mail'], true),
        );
    }

    public function test_the_reply_to_is_the_person_who_wrote_in(): void
    {
        $inquiry = Inquiry::factory()->create(['email' => 'press@example.com', 'name' => 'Sam Okafor']);

        $mail = (new InquiryReceived($inquiry))->toMail(new \stdClass);

        $this->assertSame([['press@example.com', 'Sam Okafor']], $mail->replyTo);
        $this->assertStringContainsString('Sam Okafor', $mail->subject);
    }

    /**
     * The form takes 10 MB from anyone on the internet. Whatever it accepts
     * must not end up somewhere the web server will hand back out.
     */
    public function test_an_attachment_is_stored_off_the_public_disk(): void
    {
        Notification::fake();
        Storage::fake('local');

        $this->post('/contact', [
            'contacting_for' => 'Event Invitation',
            'name' => 'Dana Cole',
            'email' => 'dana@example.com',
            'message' => 'Details are attached.',
            'file' => UploadedFile::fake()->create('brief.pdf', 64, 'application/pdf'),
        ])->assertRedirect(route('contact.show'));

        $inquiry = Inquiry::query()->sole();

        $this->assertSame('brief.pdf', $inquiry->attachment_name);
        $this->assertStringStartsWith('inquiries/', $inquiry->attachment_path);
        Storage::disk('local')->assertExists($inquiry->attachment_path);
    }

    public function test_a_rejected_submission_stores_nothing(): void
    {
        Notification::fake();

        $this->from('/contact')->post('/contact', [
            'contacting_for' => 'Not On The List',
            'name' => '',
            'email' => 'not-an-address',
            'message' => '',
        ])->assertRedirect('/contact')->assertSessionHasErrors(['contacting_for', 'name', 'email', 'message']);

        $this->assertSame(0, Inquiry::query()->count());
        Notification::assertNothingSent();
    }

    /**
     * A mail provider being down must not lose the lead or show the visitor an
     * error. The row is already written; only `notified_at` stays null.
     */
    public function test_the_lead_survives_a_mail_failure(): void
    {
        Notification::shouldReceive('route')->andThrow(new \RuntimeException('smtp is down'));

        $this->post('/contact', [
            'contacting_for' => 'Other',
            'name' => 'Robin Vale',
            'email' => 'robin@example.com',
            'message' => 'Hello.',
        ])
            ->assertRedirect(route('contact.show'))
            ->assertSessionHas('status');

        $inquiry = Inquiry::query()->sole();

        $this->assertSame('Robin Vale', $inquiry->name);
        $this->assertNull($inquiry->notified_at);
    }
}
