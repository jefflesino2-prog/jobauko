@extends('layouts.app')
@section('content')
<section class="bg-forest text-white">
  <div class="mx-auto max-w-6xl px-4 py-16 md:py-24">
    <p class="mb-3 text-sm text-gold">Bauko, Mountain Province, Philippines</p>
    <h1 class="max-w-3xl text-4xl font-bold leading-tight md:text-5xl">Discover Opportunities. Build Your Future in Bauko.</h1>
    <p class="mt-4 max-w-2xl text-lg text-white/85">JoBauko brings jobs, training, scholarships, livelihood programs, and government assistance into one place, so residents can find what they qualify for and apply without a long trip to the municipal hall.</p>
    <form action="{{ route('opportunities.index') }}" method="GET" role="search" class="mt-8 flex max-w-2xl flex-col gap-2 sm:flex-row">
      <label class="sr-only" for="home-q">Search opportunities</label>
      <input id="home-q" name="q" placeholder="Search by title or organization" class="flex-1 rounded-md px-4 py-3 text-ink placeholder:text-gray-500">
      <button class="rounded-md bg-gold px-5 py-3 font-semibold text-ink hover:brightness-95">Search</button>
    </form>
    <div class="mt-5 text-sm font-medium">
      <a href="{{ route('opportunities.index') }}" class="underline underline-offset-4">Browse all opportunities</a>
      @guest<a href="{{ route('register') }}" class="ml-5 underline underline-offset-4">Create an account</a>@endguest
    </div>
  </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-14">
  <h2 class="text-2xl font-bold">Browse by category</h2>
  <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
    @foreach (\App\Support\Catalog::CATEGORIES as $c)
      <a href="{{ route('opportunities.index', ['category' => $c]) }}" class="rounded-lg border border-gray-200 p-4 hover:border-forest hover:bg-forest-tint">
        <span class="block font-semibold">{{ $c }}</span>
        <span class="text-sm text-gray-600">{{ $counts[$c] ?? 0 }} open now</span>
      </a>
    @endforeach
  </div>
</section>

<section class="bg-mist">
  <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 lg:grid-cols-3">
    <div class="lg:col-span-2">
      <h2 class="text-2xl font-bold">Featured opportunities</h2>
      <div class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ($featured as $o)@include('partials.card', ['o' => $o, 'class' => $loop->first ? 'md:col-span-2' : ''])@endforeach
      </div>
    </div>
    <div>
      <h2 class="text-2xl font-bold">Closing soon</h2>
      <ul class="mt-6 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white">
        @foreach ($closing as $o)
          <li><a href="{{ route('opportunities.show', $o) }}" class="block p-4 hover:bg-forest-tint">
            <span class="font-medium">{{ $o->title }}</span>
            <span class="mt-1 block text-sm text-gold">@include('partials.deadline', ['o' => $o])</span>
          </a></li>
        @endforeach
      </ul>
    </div>
  </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-14">
  <h2 class="text-2xl font-bold">How it works</h2>
  <ol class="mt-6 grid gap-6 md:grid-cols-3">
    @foreach ([['Search', 'Look through listings by category, location, or deadline.'], ['Check eligibility', 'Open a listing to read who can apply and what to prepare.'], ['Apply online', 'Send a short application and follow its status.']] as $i => [$t, $d])
      <li class="flex gap-4"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold-tint font-semibold text-forest">{{ $i + 1 }}</span><span><span class="block font-semibold">{{ $t }}</span><span class="text-gray-600">{{ $d }}</span></span></li>
    @endforeach
  </ol>
  <div class="mt-12 grid gap-6 md:grid-cols-2">
    <div class="rounded-lg bg-forest-tint p-6"><h3 class="text-xl font-bold">For residents</h3><p class="mt-2 text-gray-700">One place to find programs, priority alerts for what you are looking for, and no need to travel just to ask what is open.</p></div>
    <div class="rounded-lg bg-gold-tint p-6"><h3 class="text-xl font-bold">For local organizations</h3><p class="mt-2 text-gray-700">Post openings once, reach residents across barangays, and receive applications in an organized list.</p></div>
  </div>
</section>
@endsection
