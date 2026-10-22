@extends('layouts.app')
@section('title', 'Create account')
@section('content')
@php
  $inp = 'mt-1 w-full rounded-md border border-gray-300 px-3 py-2';
  $identities = \App\Support\Catalog::IDENTITIES;
  $map = collect($identities)->map(fn ($i) => $i['categories']);
@endphp
<div class="mx-auto max-w-xl px-4 py-12">
  <h1 class="text-3xl font-bold">Create your account</h1>
  <form method="POST" action="{{ route('register') }}" class="mt-6">
    @csrf
    <label class="block text-sm font-medium">Full name<input name="name" value="{{ old('name') }}" class="{{ $inp }}"></label>
    @error('name')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    <label class="mt-4 block text-sm font-medium">Email<input name="email" type="email" value="{{ old('email') }}" class="{{ $inp }}"></label>
    @error('email')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    <div class="mt-4 grid gap-4 sm:grid-cols-2">
      <label class="block text-sm font-medium">Password<input name="password" type="password" class="{{ $inp }}"></label>
      <label class="block text-sm font-medium">Confirm password<input name="password_confirmation" type="password" class="{{ $inp }}"></label>
    </div>
    @error('password')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    <label class="mt-4 block text-sm font-medium">Mobile number (optional)<input name="phone" value="{{ old('phone') }}" inputmode="tel" class="{{ $inp }}"></label>
    @error('phone')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    <label class="mt-4 block text-sm font-medium">Barangay
      <select name="barangay" class="{{ $inp }}"><option value="">Select barangay</option>
        @foreach (\App\Support\Catalog::barangays() as $b)<option @selected(old('barangay') === $b)>{{ $b }}</option>@endforeach</select></label>
    @error('barangay')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

    <fieldset class="mt-6">
      <legend class="text-sm font-medium">I am a...</legend>
      <p class="text-sm text-gray-600">Notifications about matching opportunities are marked as priority.</p>
      <div class="mt-2 grid gap-2 sm:grid-cols-2">
        @foreach ($identities as $name => $i)
          <label class="cursor-pointer rounded-lg border border-gray-300 p-3 hover:border-forest has-[:checked]:border-forest has-[:checked]:bg-forest-tint">
            <input type="radio" name="identity" value="{{ $name }}" class="sr-only" @checked(old('identity') === $name)>
            <span class="block font-semibold">{{ $name }}</span><span class="text-sm text-gray-600">{{ $i['blurb'] }}</span>
          </label>
        @endforeach
      </div>
      @error('identity')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    </fieldset>

    <fieldset class="mt-4">
      <legend class="text-sm font-medium">Prioritize notifications for</legend>
      <div class="mt-2 grid gap-2 sm:grid-cols-2">
        @foreach (\App\Support\Catalog::CATEGORIES as $c)
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="interests[]" value="{{ $c }}" @checked(in_array($c, old('interests', []), true))>{{ $c }}</label>
        @endforeach
      </div>
      @error('interests')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    </fieldset>

    <button class="mt-6 w-full rounded-md bg-forest px-5 py-3 font-semibold text-white hover:bg-forest-dark">Create account</button>
  </form>
  <p class="mt-6 text-sm text-gray-600">Already have an account? <a href="{{ route('login') }}" class="font-medium text-forest underline">Sign in</a></p>
</div>
<script>
  // Picking an identity pre-selects the categories that fit it. They can still be changed.
  const map = @json($map);
  document.querySelectorAll('input[name=identity]').forEach(r => r.addEventListener('change', () => {
    document.querySelectorAll('input[name="interests[]"]').forEach(c => c.checked = (map[r.value] || []).includes(c.value));
  }));
</script>
@endsection
