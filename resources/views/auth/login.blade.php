@extends('layouts.app')
@section('title', 'Sign in')
@section('content')
<div class="mx-auto max-w-md px-4 py-12">
  <h1 class="text-3xl font-bold">Sign in</h1>
  <p class="mt-3 rounded-md bg-gold-tint p-3 text-sm">Demo accounts use the password <strong>password</strong>: resident@example.invalid (resident) and admin@example.invalid (administrator).</p>
  <form method="POST" action="{{ route('login') }}" class="mt-6">
    @csrf
    <label class="block text-sm font-medium">Email<input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2"></label>
    @error('email')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    <label class="mt-4 block text-sm font-medium">Password<input name="password" type="password" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2"></label>
    @error('password')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
    <button class="mt-5 w-full rounded-md bg-forest px-5 py-3 font-semibold text-white hover:bg-forest-dark">Sign in</button>
  </form>
  <p class="mt-6 text-sm text-gray-600">New here? <a href="{{ route('register') }}" class="font-medium text-forest underline">Create an account</a></p>
</div>
@endsection
