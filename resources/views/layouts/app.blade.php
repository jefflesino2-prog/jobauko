<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'JoBauko') | Bauko Opportunity Portal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
  {{-- Tailwind via CDN keeps this prototype free of a Node build step. Compile Tailwind with Vite for production. --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { forest: { DEFAULT: '#1f5a3a', dark: '#143f29', tint: '#e8f1ec' }, gold: { DEFAULT: '#c8962e', tint: '#fbf3df' }, ink: '#1d2420', mist: '#f4f5f3' },
      fontFamily: { display: ['"Source Serif 4"', 'Georgia', 'serif'], sans: ['"Public Sans"', 'system-ui', 'sans-serif'] }
    } } }
  </script>
  <style>h1,h2,h3{font-family:"Source Serif 4",Georgia,serif}</style>
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-ink antialiased">
@php $unread = auth()->check() ? auth()->user()->notices()->whereNull('read_at')->count() : 0; @endphp
<header class="sticky top-0 z-10 border-b border-gray-200 bg-white/95 backdrop-blur">
  <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
    <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="JoBauko home">
      <span class="flex h-9 w-9 items-center justify-center rounded-md bg-forest font-bold text-gold">J</span>
      <span class="font-display text-xl font-bold text-forest">JoBauko</span>
    </a>
    <nav class="flex items-center gap-1 text-sm font-medium">
      <a href="{{ route('home') }}" class="rounded px-3 py-2 hover:bg-mist">Home</a>
      <a href="{{ route('opportunities.index') }}" class="rounded px-3 py-2 hover:bg-mist">Opportunities</a>
      @auth
        @if (auth()->user()->is_admin)<a href="{{ route('admin.opportunities.index') }}" class="rounded border border-gold px-3 py-2 text-forest hover:bg-gold-tint">Admin</a>@endif
        <a href="{{ route('dashboard') }}" class="rounded px-3 py-2 hover:bg-mist">My dashboard
          @if ($unread > 0)<span class="ml-1.5 rounded-full bg-gold px-1.5 py-0.5 text-xs font-semibold text-ink">{{ $unread }}</span>@endif
        </a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded px-3 py-2 hover:bg-mist">Sign out</button></form>
      @else
        <a href="{{ route('login') }}" class="rounded px-3 py-2 hover:bg-mist">Sign in</a>
        <a href="{{ route('register') }}" class="rounded bg-forest px-3 py-2 text-white hover:bg-forest-dark">Sign up</a>
      @endauth
    </nav>
  </div>
</header>

@if (session('status'))
  <div class="mx-auto mt-4 max-w-6xl rounded-md bg-forest-tint px-4 py-3 text-forest" role="status">{{ session('status') }}</div>
@endif
@if ($errors->has('apply'))
  <div class="mx-auto mt-4 max-w-6xl rounded-md bg-red-50 px-4 py-3 text-red-800" role="alert">{{ $errors->first('apply') }}</div>
@endif

<main class="flex-1">@yield('content')</main>

<footer class="bg-forest-dark text-white/85">
  <div class="mx-auto grid max-w-6xl gap-6 px-4 py-10 md:grid-cols-3">
    <div><p class="font-display text-xl font-bold text-white">JoBauko</p><p class="mt-1 text-sm">Your Gateway to Opportunities</p></div>
    <div class="text-sm"><p class="font-semibold text-white">Contact (sample)</p><p>Municipal Hall, Bauko, Mountain Province</p><p>peso@example.invalid</p></div>
    <div class="text-sm"><p class="font-semibold text-white">Prototype notice</p><p>A student demonstration with fictional data. Not an official government service.</p></div>
  </div>
</footer>
</body>
</html>
