<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Services\Notifier;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function store(Request $request, Opportunity $opportunity)
    {
        abort_unless($opportunity->status === 'published', 404);

        if ($opportunity->isExpired()) {
            return back()->withErrors(['apply' => 'This opportunity has closed.']);
        }

        $request->merge(['contact' => preg_replace('/[\s-]/', '', (string) $request->input('contact'))]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'contact' => ['required', 'regex:/^(\+?63|0)9\d{9}$/'],
            'barangay' => ['required', Rule::in(Catalog::barangays())],
            'message' => 'nullable|string|max:1000',
        ], ['contact.regex' => 'Enter a mobile number like 0917 123 4567.']);

        $user = $request->user();
        if ($user->applications()->where('opportunity_id', $opportunity->id)->exists()) {
            return back()->withErrors(['apply' => 'You already applied to this opportunity.']);
        }

        $user->applications()->create($data + ['opportunity_id' => $opportunity->id]);
        Notifier::send($user, 'application', "Application received for {$opportunity->title}. Status: Submitted.");

        return back()->with('status', 'Application submitted. Track it on your dashboard.');
    }
}
