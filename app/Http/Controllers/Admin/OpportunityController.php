<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use App\Services\Notifier;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $q = Opportunity::withCount('applications');

        if ($s = $request->query('q')) {
            $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('provider', 'like', "%{$s}%"));
        }
        if ($c = $request->query('category')) {
            $q->where('category', $c);
        }
        if ($st = $request->query('status')) {
            $q->where('status', $st);
        }

        return view('admin.opportunities.index', ['opps' => $q->latest()->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('admin.opportunities.form', ['o' => new Opportunity(['location' => Catalog::LOCATIONS[0]])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $publish = $request->boolean('publish');

        $o = Opportunity::create($data + ['status' => $publish ? 'published' : 'unpublished']);
        if ($publish) {
            Notifier::newOpportunity($o);
        }

        return redirect()->route('admin.opportunities.index')
            ->with('status', $publish ? 'Opportunity published. Matching residents were notified.' : 'Saved as unpublished.');
    }

    public function edit(Opportunity $opportunity)
    {
        return view('admin.opportunities.form', ['o' => $opportunity]);
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $opportunity->update($this->validated($request, false));

        return redirect()->route('admin.opportunities.index')->with('status', 'Changes saved.');
    }

    /** Publish, unpublish, or archive. Publishing notifies residents (priority for matching interests). */
    public function setStatus(Request $request, Opportunity $opportunity)
    {
        $status = $request->validate(['status' => ['required', Rule::in(['published', 'unpublished', 'archived'])]])['status'];
        $wasPublished = $opportunity->status === 'published';

        $opportunity->update(['status' => $status]);
        if ($status === 'published' && ! $wasPublished) {
            Notifier::newOpportunity($opportunity);
        }

        return back()->with('status', 'Status changed to '.$status.'.');
    }

    private function validated(Request $request, bool $isNew): array
    {
        if (is_array($request->input('eligibility'))) {
            $request->merge([
                'eligibility' => implode(', ', array_filter(array_map('trim', $request->input('eligibility')))),
            ]);
        }

        return $request->validate([
            'title' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'category' => ['required', Rule::in(Catalog::CATEGORIES)],
            'location' => ['required', Rule::in(Catalog::LOCATIONS)],
            'deadline' => $isNew ? 'required|date|after_or_equal:today' : 'required|date',
            'eligibility' => 'required|string|max:255',
            'description' => 'required|string|max:3000',
        ]);
    }
}
