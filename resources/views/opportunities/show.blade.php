@extends('layouts.app')
@section('title', $o->title)
@section('content')
@php
  $expired = $o->isExpired();
  $inp = 'mt-1 w-full rounded-md border border-gray-300 px-3 py-2';
  $me = auth()->user();
@endphp
<div class="mx-auto max-w-3xl px-4 py-10">
  <a href="{{ route('opportunities.index') }}" class="text-sm font-medium text-forest hover:underline">&larr; All opportunities</a>
  <div class="mt-4 flex flex-wrap items-center gap-2">
    <span class="rounded bg-forest-tint px-2 py-1 text-xs font-medium text-forest">{{ $o->category }}</span>
    @if ($expired)<span class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold">Expired</span>@endif
    @if ($o->status !== 'published')<span class="rounded bg-gold-tint px-2 py-1 text-xs font-semibold">{{ ucfirst($o->status) }} (admin view)</span>@endif
  </div>
  <h1 class="mt-3 text-3xl font-bold leading-tight">{{ $o->title }}</h1>
  <p class="mt-1 text-gray-600">{{ $o->provider }}</p>

  <dl class="mt-6 grid gap-4 rounded-lg bg-mist p-5 sm:grid-cols-2">
    <div><dt class="text-sm text-gray-600">Location</dt><dd class="font-medium">{{ $o->location }}</dd></div>
    <div><dt class="text-sm text-gray-600">Deadline</dt><dd class="font-medium">@include('partials.deadline', ['o' => $o])</dd></div>
    <div class="sm:col-span-2"><dt class="text-sm text-gray-600">Who can apply</dt><dd class="font-medium">{{ $o->eligibility }}</dd></div>
  </dl>
  <p class="mt-6 max-w-prose leading-relaxed">{{ $o->description }}</p>

  <div class="mt-8 flex flex-wrap gap-3">
    @guest
      <a href="{{ route('login') }}" class="rounded-md bg-forest px-5 py-3 font-semibold text-white hover:bg-forest-dark">Sign in to apply</a>
    @else
      @if ($expired)
        <button disabled class="cursor-not-allowed rounded-md bg-gray-300 px-5 py-3 font-semibold text-gray-600">Applications closed</button>
      @elseif ($applied)
        <button disabled class="cursor-not-allowed rounded-md bg-gray-300 px-5 py-3 font-semibold text-gray-600">Applied: {{ $applied->status }}</button>
      @else
        <a href="#apply" class="rounded-md bg-forest px-5 py-3 font-semibold text-white hover:bg-forest-dark">Apply now</a>
      @endif
      <form method="POST" action="{{ route('save', $o) }}">@csrf
        <button class="rounded-md border border-forest px-5 py-3 font-semibold text-forest hover:bg-forest-tint">{{ $isSaved ? 'Saved' : 'Save for later' }}</button>
      </form>
    @endguest
  </div>

  @auth
    @if (! $expired && ! $applied)
      <form id="apply" method="POST" action="{{ route('apply', $o) }}" class="mt-10 rounded-lg border border-gray-200 p-6">
        @csrf
        <h2 class="text-2xl font-bold">Apply</h2>
        <p class="mt-1 text-sm text-gray-600">Demo application. It is stored in this app's database for the administrator to review.</p>
        <label class="mt-4 block text-sm font-medium">Full name<input name="name" value="{{ old('name', $me->name) }}" class="{{ $inp }}"></label>
        @error('name')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
        <label class="mt-4 block text-sm font-medium">Mobile number<input name="contact" value="{{ old('contact', $me->phone) }}" inputmode="tel" class="{{ $inp }}"></label>
        @error('contact')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
        <label class="mt-4 block text-sm font-medium">Barangay
          <select name="barangay" class="{{ $inp }}"><option value="">Select barangay</option>
            @foreach (\App\Support\Catalog::barangays() as $b)<option @selected(old('barangay', $me->barangay) === $b)>{{ $b }}</option>@endforeach</select></label>
        @error('barangay')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
        <label class="mt-4 block text-sm font-medium">Why are you applying? (optional)<textarea name="message" rows="3" class="{{ $inp }}">{{ old('message') }}</textarea></label>
        <button class="mt-6 rounded-md bg-forest px-5 py-2 font-semibold text-white hover:bg-forest-dark">Submit application</button>
      </form>
    @endif
  @endauth
</div>
@endsection
