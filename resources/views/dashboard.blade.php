@extends('layouts.app')
@section('title', 'My dashboard')
@section('content')
@php
  $unread = $user->notices()->whereNull('read_at')->count();
  $tabs = ['foryou' => 'For you', 'applications' => 'My applications', 'saved' => 'Saved', 'notices' => 'Notifications'.($unread ? " ({$unread})" : ''), 'sms' => 'SMS Notification Demo', 'profile' => 'Profile'];
  $inp = 'mt-1 w-full rounded-md border border-gray-300 px-3 py-2';
  $steps = ['Submitted', 'Under Review', 'Shortlisted', 'Accepted'];
@endphp
<div class="mx-auto max-w-6xl px-4 py-10">
  <h1 class="text-3xl font-bold">Welcome, {{ $user->name }}</h1>
  <p class="mt-1 text-gray-600">{{ $user->identity }} · {{ $user->barangay }}, Bauko</p>

  <div class="mt-6 grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2">
      <div role="tablist" class="flex flex-wrap gap-1 border-b border-gray-200">
        @foreach ($tabs as $k => $label)
          <a href="{{ route('dashboard', ['tab' => $k]) }}" role="tab" aria-selected="{{ $tab === $k ? 'true' : 'false' }}"
             class="-mb-px border-b-2 px-4 py-2 text-sm font-medium {{ $tab === $k ? 'border-forest text-forest' : 'border-transparent text-gray-600 hover:text-ink' }}">{{ $label }}</a>
        @endforeach
      </div>

      @if ($tab === 'foryou')
        <p class="mt-4 text-sm text-gray-600">Matched to your profile as a {{ strtolower($user->identity ?? 'resident') }}: {{ implode(', ', $user->interests ?? []) ?: 'no categories selected' }}.</p>
        @forelse ($opps as $o)
          <div class="mt-3 rounded-lg border border-gray-200 p-4">
            <a href="{{ route('opportunities.show', $o) }}" class="font-semibold hover:text-forest hover:underline">{{ $o->title }}</a>
            <p class="text-sm text-gray-600">{{ $o->provider }} · {{ $o->category }}</p>
            <p class="mt-1 text-sm text-gold">@include('partials.deadline', ['o' => $o])</p>
          </div>
        @empty
          <p class="mt-4 rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-600">Nothing open matches your categories right now. You can change them in your profile.</p>
        @endforelse
      @endif

      @if ($tab === 'applications')
        @forelse ($applications as $a)
          @php $at = array_search($a->status, $steps, true); $done = in_array($a->status, ['Accepted', 'Rejected'], true); @endphp
          <div class="mt-3 rounded-lg border border-gray-200 p-4">
            <div class="flex flex-wrap items-start justify-between gap-2">
              <div>
                <a href="{{ route('opportunities.show', $a->opportunity) }}" class="font-semibold hover:text-forest hover:underline">{{ $a->opportunity->title }}</a>
                <p class="text-sm text-gray-600">{{ $a->opportunity->provider }} · Applied {{ $a->created_at->format('M j, Y') }}</p>
              </div>
              <span class="rounded bg-forest-tint px-2 py-1 text-xs font-semibold text-forest">{{ $a->status }}</span>
            </div>
            @if ($a->status === 'Rejected')
              <p class="mt-3 text-sm font-medium text-red-700">Rejected. This application was not selected.</p>
            @else
              <ol class="mt-3 grid grid-cols-4 gap-1">
                @foreach ($steps as $i => $s)
                  <li><div class="h-1.5 rounded {{ $i <= $at ? 'bg-forest' : 'bg-gray-200' }}"></div>
                    <span class="mt-1 block text-xs {{ $i === $at ? 'font-semibold text-forest' : 'text-gray-500' }}">{{ $s }}</span></li>
                @endforeach
              </ol>
            @endif
            @unless ($done)
              <form method="POST" action="{{ route('applications.advance', $a) }}" class="mt-3">@csrf
                <button class="text-sm font-medium text-forest underline underline-offset-4">Demo: simulate status update</button>
              </form>
            @endunless
          </div>
        @empty
          <p class="mt-4 rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-600">You have not applied yet. <a href="{{ route('opportunities.index') }}" class="font-medium text-forest underline">Browse opportunities</a>.</p>
        @endforelse
      @endif

      @if ($tab === 'saved')
        @forelse ($saved as $o)
          <div class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-gray-200 p-4">
            <div>
              <a href="{{ route('opportunities.show', $o) }}" class="font-semibold hover:text-forest hover:underline">{{ $o->title }}</a>
              <p class="text-sm text-gray-600">{{ $o->provider }} · {{ $o->isExpired() ? 'Expired' : 'Closes '.$o->deadline->format('M j, Y') }}</p>
            </div>
            <form method="POST" action="{{ route('save', $o) }}">@csrf<button class="text-sm font-medium text-red-700 hover:underline">Remove</button></form>
          </div>
        @empty
          <p class="mt-4 rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-600">No saved opportunities. Use Save on a listing to keep it here.</p>
        @endforelse
      @endif

      @if ($tab === 'notices')
        <div class="mt-4 flex items-center justify-between">
          <p class="text-sm text-gray-600">{{ $unread }} unread</p>
          <form method="POST" action="{{ route('notices.readAll') }}">@csrf<button class="text-sm font-medium text-forest underline underline-offset-4">Mark all as read</button></form>
        </div>
        @forelse ($notices as $n)
          <div class="mt-2 flex items-start justify-between gap-3 rounded-lg border p-4 {{ $n->read_at ? 'border-gray-200' : 'border-gold bg-gold-tint' }}">
            <div>
              <p class="text-sm">@if ($n->priority)<span class="mr-2 rounded bg-gold px-1.5 py-0.5 text-xs font-semibold text-ink">Priority</span>@endif{{ $n->text }}</p>
              <p class="mt-1 text-xs text-gray-600">{{ $n->created_at->format('M j, Y') }}</p>
            </div>
            @unless ($n->read_at)
              <form method="POST" action="{{ route('notices.read', $n) }}">@csrf<button class="shrink-0 text-sm font-medium text-forest hover:underline">Mark read</button></form>
            @endunless
          </div>
        @empty
          <p class="mt-4 rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-600">No notifications yet.</p>
        @endforelse
      @endif

      @if ($tab === 'sms')
        <p class="mt-4 rounded-md bg-gold-tint p-3 text-sm">No text message is sent. This panel only shows what a reminder could look like. Real SMS needs an SMS provider (and queued jobs on this server).</p>
        @forelse ($opps as $o)
          @php $d = $o->daysLeft(); @endphp
          <div class="mt-3 rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-600">To: {{ $user->phone ? '•••• ••• '.substr($user->phone, -3) : 'no number on profile' }}</p>
            <p class="mt-2 rounded-lg bg-mist p-3 text-sm">JoBauko reminder: "{{ $o->title }}" closes {{ $d === 0 ? 'today' : ($d === 1 ? 'tomorrow' : "in {$d} days") }} ({{ $o->deadline->format('M j, Y') }}). Apply before the deadline.</p>
            <div class="mt-3 flex items-center justify-between gap-3">
              <span class="text-sm" aria-live="polite">Simulated delivery: <span id="sms-{{ $o->id }}" class="font-semibold">Not sent</span></span>
              <button type="button" data-sms="sms-{{ $o->id }}" class="rounded-md border border-forest px-3 py-1.5 text-sm font-medium text-forest hover:bg-forest-tint disabled:opacity-50">Send demo reminder</button>
            </div>
          </div>
        @empty
          <p class="mt-4 text-sm text-gray-600">No opportunities close within 14 days, so there are no sample reminders.</p>
        @endforelse
        <script>
          document.querySelectorAll('[data-sms]').forEach(btn => btn.addEventListener('click', () => {
            const out = document.getElementById(btn.dataset.sms);
            btn.disabled = true; out.textContent = 'Queued';
            setTimeout(() => out.textContent = 'Sent', 800);
            setTimeout(() => { out.textContent = 'Delivered'; btn.disabled = false; btn.textContent = 'Send again (demo)'; }, 1800);
          }));
        </script>
      @endif

      @if ($tab === 'profile')
        <form method="POST" action="{{ route('profile.update') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
          @csrf @method('PUT')
          <label class="text-sm font-medium">Full name<input name="name" value="{{ old('name', $user->name) }}" class="{{ $inp }}"></label>
          <label class="text-sm font-medium">Email<input value="{{ $user->email }}" readonly class="{{ $inp }} bg-mist"></label>
          <label class="text-sm font-medium">Mobile number<input name="phone" value="{{ old('phone', $user->phone) }}" class="{{ $inp }}"></label>
          <label class="text-sm font-medium">Barangay
            <select name="barangay" class="{{ $inp }}">@foreach (\App\Support\Catalog::barangays() as $b)<option @selected(old('barangay', $user->barangay) === $b)>{{ $b }}</option>@endforeach</select></label>
          <label class="text-sm font-medium">Education<input name="education" value="{{ old('education', $user->education) }}" class="{{ $inp }}"></label>
          <label class="text-sm font-medium">Skills<input name="skills" value="{{ old('skills', $user->skills) }}" class="{{ $inp }}"></label>
          <label class="text-sm font-medium sm:col-span-2">I am a
            <select name="identity" class="{{ $inp }}">@foreach (array_keys(\App\Support\Catalog::IDENTITIES) as $i)<option @selected(old('identity', $user->identity) === $i)>{{ $i }}</option>@endforeach</select></label>
          <fieldset class="sm:col-span-2"><legend class="text-sm font-medium">Prioritize notifications for</legend>
            <div class="mt-2 grid gap-2 sm:grid-cols-2">
              @foreach (\App\Support\Catalog::CATEGORIES as $c)
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="interests[]" value="{{ $c }}" @checked(in_array($c, old('interests', $user->interests ?? []), true))>{{ $c }}</label>
              @endforeach
            </div></fieldset>
          @if ($errors->any())<div class="text-sm text-red-700 sm:col-span-2" role="alert">@foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif
          <div class="sm:col-span-2"><button class="rounded-md bg-forest px-5 py-2 font-semibold text-white hover:bg-forest-dark">Save profile</button></div>
        </form>
      @endif
    </div>

    <aside>
      <h2 class="text-xl font-bold">Upcoming deadlines</h2>
      @if ($deadlines->isEmpty())
        <p class="mt-3 text-sm text-gray-600">Save or apply to opportunities to track their deadlines here.</p>
      @else
        <ul class="mt-3 divide-y divide-gray-200 rounded-lg border border-gray-200">
          @foreach ($deadlines as $o)
            <li><a href="{{ route('opportunities.show', $o) }}" class="block p-3 hover:bg-forest-tint"><span class="text-sm font-medium">{{ $o->title }}</span>
              <span class="mt-0.5 block text-xs text-gold">@include('partials.deadline', ['o' => $o])</span></a></li>
          @endforeach
        </ul>
      @endif
    </aside>
  </div>
</div>
@endsection
