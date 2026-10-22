<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Notice;
use App\Models\Opportunity;
use App\Services\Notifier;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tab = $request->query('tab', 'foryou');
        $open = fn () => Opportunity::published()->whereDate('deadline', '>=', today());

        $data = ['user' => $user, 'tab' => $tab, 'opps' => collect(), 'applications' => collect(), 'saved' => collect(), 'notices' => collect()];

        switch ($tab) {
            case 'applications':
                $data['applications'] = $user->applications()->with('opportunity')->latest()->get();
                break;
            case 'saved':
                $data['saved'] = $user->savedOpportunities()->get();
                break;
            case 'notices':
                $data['notices'] = $user->notices()->orderByRaw('(read_at is null) desc')->orderByDesc('priority')->latest()->get();
                break;
            case 'sms':
                $data['opps'] = Opportunity::published()->whereBetween('deadline', [today(), today()->addDays(14)])->orderBy('deadline')->take(4)->get();
                break;
            case 'profile':
                break;
            default:
                $data['tab'] = 'foryou';
                $data['opps'] = $open()->whereIn('category', $user->interests ?? [])->orderBy('deadline')->get();
        }

        $ids = $user->savedOpportunities()->pluck('opportunities.id')->merge($user->applications()->pluck('opportunity_id'))->unique();
        $data['deadlines'] = Opportunity::whereIn('id', $ids)->whereDate('deadline', '>=', today())->orderBy('deadline')->take(5)->get();

        return view('dashboard', $data);
    }

    public function updateProfile(Request $request)
    {
        $request->merge(['phone' => preg_replace('/[\s-]/', '', (string) $request->input('phone'))]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => ['nullable', 'regex:/^(\+?63|0)9\d{9}$/'],
            'barangay' => ['required', Rule::in(Catalog::barangays())],
            'education' => 'nullable|string|max:120',
            'skills' => 'nullable|string|max:255',
            'identity' => ['required', Rule::in(array_keys(Catalog::IDENTITIES))],
            'interests' => 'required|array|min:1',
            'interests.*' => [Rule::in(Catalog::CATEGORIES)],
        ], ['interests.required' => 'Pick at least one category.']);

        $request->user()->update($data);

        return redirect()->route('dashboard', ['tab' => 'profile'])->with('status', 'Profile saved.');
    }

    public function markRead(Request $request, Notice $notice)
    {
        abort_unless($notice->user_id === $request->user()->id, 403);
        $notice->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->notices()->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }

    /** Demo only: stands in for the administrator moving an application forward. */
    public function advance(Request $request, Application $application)
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        $order = ['Submitted', 'Under Review', 'Shortlisted', 'Accepted'];
        $i = array_search($application->status, $order, true);
        if ($i !== false && isset($order[$i + 1])) {
            $application->update(['status' => $order[$i + 1]]);
            Notifier::status($application);
        }

        return back();
    }
}
