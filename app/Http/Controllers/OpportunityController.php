<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $q = Opportunity::published();

        if ($s = $request->query('q')) {
            $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('provider', 'like', "%{$s}%"));
        }
        if ($c = $request->query('category')) {
            $q->where('category', $c);
        }
        if ($l = $request->query('location')) {
            $q->where('location', $l);
        }
        match ($request->query('deadline')) {
            'open' => $q->whereDate('deadline', '>=', today()),
            '7' => $q->whereBetween('deadline', [today(), today()->addDays(7)]),
            '30' => $q->whereBetween('deadline', [today(), today()->addDays(30)]),
            'expired' => $q->whereDate('deadline', '<', today()),
            default => null,
        };

        if ($request->query('sort') === 'closing') {
            // Open listings by nearest deadline, expired ones last.
            $q->orderByRaw('deadline < ?', [today()->toDateString()])->orderBy('deadline');
        } else {
            $q->orderByDesc('created_at');
        }

        return view('opportunities.index', [
            'opps' => $q->paginate(9)->withQueryString(),
            'saved' => $request->user() ? $request->user()->savedOpportunities()->pluck('opportunities.id')->all() : [],
        ]);
    }

    public function show(Request $request, Opportunity $opportunity)
    {
        abort_unless($opportunity->status === 'published' || $request->user()?->is_admin, 404);
        $user = $request->user();

        return view('opportunities.show', [
            'o' => $opportunity,
            'isSaved' => $user ? $user->savedOpportunities()->where('opportunities.id', $opportunity->id)->exists() : false,
            'applied' => $user ? $user->applications()->where('opportunity_id', $opportunity->id)->first() : null,
        ]);
    }
}
