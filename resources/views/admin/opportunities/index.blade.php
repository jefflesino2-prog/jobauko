@extends('layouts.app')
@section('title', 'Manage opportunities')
@section('content')
@php
  $field = 'rounded-md border border-gray-300 bg-white px-3 py-2 text-sm';
  $badge = ['published' => 'bg-forest-tint text-forest', 'unpublished' => 'bg-gold-tint text-ink', 'archived' => 'bg-gray-200 text-gray-700'];
  $btn = 'rounded border border-gray-300 px-2 py-1 text-sm hover:bg-mist';
@endphp
<div class="mx-auto max-w-6xl px-4 py-10">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div><h1 class="text-3xl font-bold">Manage opportunities</h1><p class="mt-1 text-gray-600">Publish programs and opportunities for residents.</p></div>
    <a href="{{ route('admin.opportunities.create') }}" class="rounded-md bg-forest px-4 py-2 font-semibold text-white hover:bg-forest-dark">Add opportunity</a>
  </div>

  <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
    <label class="text-sm font-medium">Search<input name="q" value="{{ request('q') }}" placeholder="Title or provider" class="{{ $field }} mt-1 block"></label>
    <label class="text-sm font-medium">Category
      <select name="category" class="{{ $field }} mt-1 block"><option value="">All</option>
        @foreach (\App\Support\Catalog::CATEGORIES as $c)<option @selected(request('category') === $c)>{{ $c }}</option>@endforeach</select></label>
    <label class="text-sm font-medium">Status
      <select name="status" class="{{ $field }} mt-1 block"><option value="">All</option>
        @foreach (['published', 'unpublished', 'archived'] as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach</select></label>
    <button class="rounded-md bg-forest px-4 py-2 text-sm font-semibold text-white hover:bg-forest-dark">Filter</button>
    <a href="{{ route('admin.opportunities.index') }}" class="px-3 py-2 text-sm hover:underline">Clear</a>
  </form>

  @if ($opps->isEmpty())
    <p class="mt-6 rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-600">No opportunities match. Change the filters or add a new one.</p>
  @else
    <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200">
      <table class="w-full min-w-[820px] text-left text-sm">
        <thead class="bg-mist"><tr><th class="p-3">Opportunity</th><th class="p-3">Deadline</th><th class="p-3">Status</th><th class="p-3">Applications</th><th class="p-3">Actions</th></tr></thead>
        <tbody class="divide-y divide-gray-200">
          @foreach ($opps as $o)
            <tr>
              <td class="p-3"><p class="font-medium">{{ $o->title }}</p><p class="text-gray-600">{{ $o->category }}</p></td>
              <td class="p-3">{{ $o->deadline->format('M j, Y') }}@if ($o->isExpired()) <span class="text-gray-500">(expired)</span>@endif</td>
              <td class="p-3"><span class="rounded px-2 py-1 text-xs font-semibold {{ $badge[$o->status] ?? '' }}">{{ ucfirst($o->status) }}</span></td>
              <td class="p-3">{{ $o->applications_count }}</td>
              <td class="p-3">
                <div class="flex flex-wrap gap-1">
                  <a href="{{ route('admin.opportunities.edit', $o) }}" class="{{ $btn }}">Edit</a>
                  @if ($o->status === 'published')
                    <form method="POST" action="{{ route('admin.opportunities.status', $o) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="unpublished"><button class="{{ $btn }}">Unpublish</button></form>
                  @else
                    <form method="POST" action="{{ route('admin.opportunities.status', $o) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="published"><button class="{{ $btn }}">Publish</button></form>
                  @endif
                  @if ($o->status === 'archived')
                    <form method="POST" action="{{ route('admin.opportunities.status', $o) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="unpublished"><button class="{{ $btn }}">Restore</button></form>
                  @else
                    <form method="POST" action="{{ route('admin.opportunities.status', $o) }}" onsubmit="return confirm('Archive this opportunity? It will be hidden from residents.')">@csrf @method('PATCH')<input type="hidden" name="status" value="archived"><button class="{{ $btn }}">Archive</button></form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $opps->links() }}</div>
  @endif
</div>
@endsection
