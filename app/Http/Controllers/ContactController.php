<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Notifications\InquiryReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contacting_for' => ['required', 'string', 'in:SM Partnership Inquiry,Website Press Release,Event Invitation,Other'],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $inquiry = Inquiry::query()->create($validated + [
            // Attachments go to a private disk. The form takes 10 MB from
            // anyone on the internet, so what it accepts must not be servable.
            'attachment_path' => $request->file('file')?->store('inquiries'),
            'attachment_name' => $request->file('file')?->getClientOriginalName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notify($inquiry);

        return redirect()
            ->route('contact.show')
            ->with('status', "Thanks for reaching out. We'll get back to you soon.");
    }

    /**
     * Mail the inquiry on, and record whether that worked.
     *
     * A mail failure must not lose the lead or show the visitor an error: the
     * row is already saved, so the recoverable outcome is a logged failure and
     * a `notified_at` that stays null.
     */
    private function notify(Inquiry $inquiry): void
    {
        $recipients = array_filter((array) config('site.email'));

        if ($recipients === []) {
            return;
        }

        try {
            Notification::route('mail', $recipients)->notify(new InquiryReceived($inquiry));

            $inquiry->forceFill(['notified_at' => now()])->save();
        } catch (Throwable $e) {
            report($e);
        }
    }
}
