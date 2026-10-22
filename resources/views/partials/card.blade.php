@php
  $expired = $o->isExpired();
  $isDetailed = $detailed ?? false;
  $isSavedItem = isset($saved) && in_array($o->id, $saved);
@endphp

@if ($isDetailed)
  <article class="group relative flex flex-col justify-between rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-forest/40 hover:shadow-md {{ $class ?? '' }}">
    <div>
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center rounded-full bg-forest-tint px-3 py-1 text-xs font-semibold text-forest">
            {{ $o->category }}
          </span>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-mist px-3 py-1 text-xs font-medium text-gray-700">
            <svg class="h-3.5 w-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $o->location }}
          </span>
          @if ($expired)
            <span class="inline-flex items-center rounded-full bg-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-700">Expired</span>
          @endif
        </div>

        @isset($saved)
          @auth
            <form method="POST" action="{{ route('save', $o) }}">@csrf
              <button class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-forest transition hover:border-forest hover:bg-forest-tint" aria-pressed="{{ $isSavedItem ? 'true' : 'false' }}">
                <svg class="h-4 w-4 {{ $isSavedItem ? 'fill-forest text-forest' : 'fill-none text-forest' }}" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <span>{{ $isSavedItem ? 'Saved' : 'Save opportunity' }}</span>
              </button>
            </form>
          @else
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-forest transition hover:border-forest hover:bg-forest-tint">
              <svg class="h-4 w-4 fill-none text-forest" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
              Save opportunity
            </a>
          @endauth
        @endisset
      </div>

      <div class="mt-4">
        <h2 class="text-xl font-bold leading-snug md:text-2xl">
          <a href="{{ route('opportunities.show', $o) }}" class="transition hover:text-forest hover:underline">
            {{ $o->title }}
          </a>
        </h2>
        <p class="mt-1 text-sm font-medium text-gray-600">
          Posted by: <span class="font-semibold text-ink">{{ $o->provider }}</span>
        </p>
      </div>

      @if ($o->description)
        <p class="mt-3 text-sm leading-relaxed text-gray-700">
          {{ $o->description }}
        </p>
      @endif

      @if ($o->eligibility)
        <div class="mt-4 rounded-lg border border-gray-100 bg-mist/90 p-3.5 text-sm text-gray-700">
          <span class="font-semibold text-ink">Eligibility:</span> {{ $o->eligibility }}
        </div>
      @endif
    </div>

    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
      <div class="flex items-center gap-2 text-sm">
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Deadline:</span>
        <span class="font-semibold {{ $expired ? 'text-gray-500' : 'text-gold' }}">
          @include('partials.deadline', ['o' => $o])
        </span>
      </div>

      <div class="flex items-center gap-3">
        <a href="{{ route('opportunities.show', $o) }}" class="inline-flex items-center gap-1.5 rounded-md bg-forest px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-forest-dark">
          <span>View details & apply</span>
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </a>
      </div>
    </div>
  </article>
@else
  <article class="flex flex-col rounded-lg border border-gray-200 bg-white p-5 {{ $class ?? '' }}">
    <div class="flex items-start justify-between gap-3">
      <span class="rounded bg-forest-tint px-2 py-1 text-xs font-medium text-forest">{{ $o->category }}</span>
      <div class="flex items-center gap-2">
        @if ($expired)<span class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold text-gray-700">Expired</span>@endif
        @isset($saved)
          @auth
            <form method="POST" action="{{ route('save', $o) }}">@csrf
              <button class="rounded px-2 py-1 text-xs font-medium text-forest hover:bg-forest-tint" aria-pressed="{{ in_array($o->id, $saved) ? 'true' : 'false' }}">{{ in_array($o->id, $saved) ? 'Saved' : 'Save' }}</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="rounded px-2 py-1 text-xs font-medium text-forest hover:bg-forest-tint">Save</a>
          @endauth
        @endisset
      </div>
    </div>
    <h3 class="mt-3 text-lg font-bold leading-snug"><a href="{{ route('opportunities.show', $o) }}" class="hover:text-forest hover:underline">{{ $o->title }}</a></h3>
    <p class="text-sm text-gray-600">{{ $o->provider }}</p>
    <p class="mt-3 text-sm text-gray-700"><span class="font-medium">Eligibility:</span> {{ $o->eligibility }}</p>
    <div class="mt-auto flex flex-wrap gap-x-4 gap-y-1 pt-4 text-sm text-gray-600">
      <span>{{ $o->location }}</span>
      <span class="{{ $expired ? '' : 'text-gold' }}">@include('partials.deadline', ['o' => $o])</span>
    </div>
  </article>
@endif
