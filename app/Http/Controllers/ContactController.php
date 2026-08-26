<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'contacting_for' => ['required', 'string', 'in:SM Partnership Inquiry,Website Press Release,Event Invitation,Other'],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        return redirect()
            ->route('contact.show')
            ->with('status', "Thanks for reaching out. We'll get back to you soon.");
    }
}
