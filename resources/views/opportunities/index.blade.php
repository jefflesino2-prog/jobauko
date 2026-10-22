@extends('layouts.app')
@section('title', 'Opportunities')
@section('content')
@php $field = 'w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm'; @endphp
<div class="mx-auto max-w-6xl px-4 py-10">
  <h1 class="text-3xl font-bold">Opportunities</h1>
  <p class="mt-1 text-gray-600">Sample listings for demonstration. None are real announcements.</p>

  <form method="GET" class="mt-6 grid gap-3 rounded-lg bg-mist p-4 md:grid-cols-6">
    <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium" for="q">Search</label>
      <input id="q" name="q" value="{{ request('q') }}" placeholder="Title or organization" class="{{ $field }}"></div>
    <div><label class="mb-1 block text-sm font-medium" for="category">Category</label>
      <select id="category" name="category" class="{{ $field }}"><option value="">All</option>
        @foreach (\App\Support\Catalog::CATEGORIES as $c)<option @selected(request('category') === $c)>{{ $c }}</option>@endforeach</select></div>
    <div><label class="mb-1 block text-sm font-medium" for="location">Location</label>
      <select id="location" name="location" class="{{ $field }}"><option value="">All</option>
        @foreach (\App\Support\Catalog::LOCATIONS as $l)<option @selected(request('location') === $l)>{{ $l }}</option>@endforeach</select></div>
    <div><label class="mb-1 block text-sm font-medium" for="deadline">Deadline</label>
      <select id="deadline" name="deadline" class="{{ $field }}">
        <option value="">Any</option>
        @foreach (['open' => 'Still open', '7' => 'Within 7 days', '30' => 'Within 30 days', 'expired' => 'Expired'] as $v => $l)<option value="{{ $v }}" @selected((string) request('deadline') === (string) $v)>{{ $l }}</option>@endforeach</select></div>
    <div><label class="mb-1 block text-sm font-medium" for="sort">Sort by</label>
      <select id="sort" name="sort" class="{{ $field }}">
        <option value="newest" @selected(request('sort') !== 'closing')>Newest</option>
        <option value="closing" @selected(request('sort') === 'closing')>Closing soon</option></select></div>
    <div class="flex gap-2 md:col-span-6">
      <button class="rounded-md bg-forest px-4 py-2 font-semibold text-white hover:bg-forest-dark">Apply filters</button>
      <a href="{{ route('opportunities.index') }}" class="rounded-md px-4 py-2 font-medium hover:bg-white">Clear</a>
    </div>
  </form>

  <p class="mt-4 text-sm text-gray-600">{{ $opps->total() }} {{ \Illuminate\Support\Str::plural('result', $opps->total()) }}</p>

  @if ($opps->isEmpty())
    <div class="mx-auto mt-6 max-w-md rounded-lg border border-dashed border-gray-300 p-10 text-center">
      <p class="font-semibold">No opportunities match your filters</p>
      <a href="{{ route('opportunities.index') }}" class="mt-4 inline-block rounded-md bg-forest px-4 py-2 font-medium text-white">Clear filters</a>
    </div>
  @else
    <div class="mt-6 grid grid-cols-1 gap-5">
      @foreach ($opps as $o)
        @include('partials.card', ['o' => $o, 'saved' => $saved, 'detailed' => true])
      @endforeach
    </div>
    <div class="mt-6">{{ $opps->links() }}</div>
  @endif
</div>
@endsection
