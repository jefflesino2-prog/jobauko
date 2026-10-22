@extends('layouts.app')
@section('title', $o->exists ? 'Edit opportunity' : 'Add opportunity')
@section('content')
@php $inp = 'mt-1 w-full rounded-md border border-gray-300 px-3 py-2'; @endphp
<div class="mx-auto max-w-2xl px-4 py-10">
  <a href="{{ route('admin.opportunities.index') }}" class="text-sm font-medium text-forest hover:underline">&larr; All opportunities</a>
  <h1 class="mt-3 text-3xl font-bold">{{ $o->exists ? 'Edit opportunity' : 'Add opportunity' }}</h1>

  <form method="POST" action="{{ $o->exists ? route('admin.opportunities.update', $o) : route('admin.opportunities.store') }}" class="mt-6">
    @csrf
    @if ($o->exists) @method('PUT') @endif

    <label class="block text-sm font-medium">Title<input name="title" value="{{ old('title', $o->title) }}" class="{{ $inp }}"></label>
    @error('title')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

    <label class="mt-4 block text-sm font-medium">Provider / organization<input name="provider" value="{{ old('provider', $o->provider) }}" class="{{ $inp }}"></label>
    @error('provider')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

    <div class="mt-4 grid gap-4 sm:grid-cols-2">
      <label class="block text-sm font-medium">Category
        <select name="category" class="{{ $inp }}">@foreach (\App\Support\Catalog::CATEGORIES as $c)<option @selected(old('category', $o->category) === $c)>{{ $c }}</option>@endforeach</select></label>
      <label class="block text-sm font-medium">Location
        <select name="location" class="{{ $inp }}">@foreach (\App\Support\Catalog::LOCATIONS as $l)<option @selected(old('location', $o->location) === $l)>{{ $l }}</option>@endforeach</select></label>
    </div>
    @error('category')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror
    @error('location')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

    <label class="mt-4 block text-sm font-medium">Application deadline
      <input type="date" name="deadline" value="{{ old('deadline', optional($o->deadline)->format('Y-m-d')) }}" class="{{ $inp }}"></label>
    @error('deadline')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

    {{-- Who can apply / Stacked Eligibility Selector --}}
    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm" id="eligibility-stack-wrapper">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <label for="eligibility-input" class="text-sm font-semibold text-ink">
          Who can apply (Eligibility & Stacked Requirements)
        </label>
        <span class="text-xs text-gray-500">
          <span id="char-count">0</span> / 255 characters
        </span>
      </div>
      <p class="mt-1 text-xs text-gray-600">
        Select from preset resident groups, jobs, or skills below to stack them into the eligibility criteria, or type directly into the field.
      </p>

      {{-- Currently Stacked Tags --}}
      <div class="mt-3 min-h-[46px] rounded-md border border-dashed border-gray-300 bg-mist/60 p-2.5">
        <div class="flex items-center justify-between gap-2 pb-1.5 text-xs font-medium text-gray-600">
          <span class="flex items-center gap-1.5">
            <span class="inline-block h-2 w-2 rounded-full bg-forest"></span>
            <span>Currently stacked (<span id="stack-count">0</span>):</span>
          </span>
          <button type="button" id="clear-stack-btn" class="hidden text-xs font-semibold text-red-600 hover:underline">Clear all</button>
        </div>
        <div id="stacked-chips-container" class="flex flex-wrap gap-1.5">
          <span id="empty-stack-msg" class="text-xs italic text-gray-500">No criteria selected yet. Click any preset job, skill, or requirement below to add to your stack.</span>
        </div>
      </div>

      {{-- Synced Form Field --}}
      <div class="mt-3">
        <textarea id="eligibility-input" name="eligibility" rows="2" class="{{ $inp }} text-sm" placeholder="e.g. Bauko resident (18 or older), Basic computer & digital tools, Administrative & Office Clerk">{{ old('eligibility', $o->eligibility) }}</textarea>
      </div>
      @error('eligibility')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

      {{-- Preset Criteria Selector Tabs and Search --}}
      <div class="mt-4 border-t border-gray-100 pt-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Click to stack jobs & skills:</span>
          <input type="text" id="preset-search" placeholder="Search presets..." class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs placeholder:text-gray-400 focus:border-forest focus:outline-none">
        </div>

        {{-- Category Tabs --}}
        <div class="mt-2 flex flex-wrap gap-1 border-b border-gray-200 pb-2 text-xs" id="category-tabs">
          <button type="button" data-tab="all" class="category-tab-btn rounded px-2.5 py-1 font-medium bg-forest text-white">All</button>
          @foreach (\App\Support\Catalog::ELIGIBILITY_PRESETS as $catTitle => $presets)
            <button type="button" data-tab="{{ Str::slug($catTitle) }}" class="category-tab-btn rounded px-2.5 py-1 font-medium bg-gray-100 text-gray-700 hover:bg-forest-tint hover:text-forest">
              {{ $catTitle }}
            </button>
          @endforeach
        </div>

        {{-- Preset Pills Groups --}}
        <div class="mt-3 max-h-56 overflow-y-auto pr-1 space-y-3" id="presets-container">
          @foreach (\App\Support\Catalog::ELIGIBILITY_PRESETS as $catTitle => $presets)
            <div class="preset-category-group" data-group="{{ Str::slug($catTitle) }}">
              <p class="text-xs font-semibold text-gray-600 mb-1.5 flex items-center gap-1">
                <span>{{ $catTitle }}</span>
              </p>
              <div class="flex flex-wrap gap-1.5">
                @foreach ($presets as $preset)
                  <button type="button" data-preset="{{ $preset }}" class="preset-pill inline-flex items-center gap-1 rounded-md border border-gray-200 bg-mist px-2.5 py-1 text-xs text-gray-800 transition hover:border-forest hover:bg-forest-tint hover:text-forest">
                    <span class="preset-icon text-gray-400 font-bold">+</span>
                    <span class="preset-label">{{ $preset }}</span>
                  </button>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>

        {{-- Custom Item Adder --}}
        <div class="mt-3 flex gap-2 pt-2 border-t border-gray-100">
          <input type="text" id="custom-criterion-input" placeholder="Custom job, skill, or qualification..." class="flex-1 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs focus:border-forest focus:outline-none">
          <button type="button" id="add-custom-btn" class="rounded-md bg-forest px-3 py-1.5 text-xs font-semibold text-white hover:bg-forest-dark">
            + Add to stack
          </button>
        </div>
      </div>
    </div>

    <label class="mt-4 block text-sm font-medium">Description<textarea name="description" rows="5" class="{{ $inp }}">{{ old('description', $o->description) }}</textarea></label>
    @error('description')<p class="mt-1 text-sm text-red-700" role="alert">{{ $message }}</p>@enderror

    @unless ($o->exists)
      <label class="mt-5 flex items-start gap-2 text-sm"><input type="checkbox" name="publish" value="1" class="mt-1" @checked(old('publish', true))>
        <span><span class="font-medium">Publish now</span><br><span class="text-gray-600">Residents are notified. Those whose interests match get a priority notice. Leave unchecked to save as a draft.</span></span></label>
    @endunless

    <div class="mt-6 flex gap-3">
      <button class="rounded-md bg-forest px-5 py-2 font-semibold text-white hover:bg-forest-dark">{{ $o->exists ? 'Save changes' : 'Save opportunity' }}</button>
      <a href="{{ route('admin.opportunities.index') }}" class="rounded-md px-4 py-2 font-medium hover:bg-mist">Cancel</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('eligibility-input');
  const stackedContainer = document.getElementById('stacked-chips-container');
  const emptyMsg = document.getElementById('empty-stack-msg');
  const clearBtn = document.getElementById('clear-stack-btn');
  const charCount = document.getElementById('char-count');
  const stackCount = document.getElementById('stack-count');
  const presetPills = document.querySelectorAll('.preset-pill');
  const searchInput = document.getElementById('preset-search');
  const tabBtns = document.querySelectorAll('.category-tab-btn');
  const groupDivs = document.querySelectorAll('.preset-category-group');
  const customInput = document.getElementById('custom-criterion-input');
  const customAddBtn = document.getElementById('add-custom-btn');

  let currentStack = [];

  function parseFromText(text) {
    if (!text) return [];
    return text.split(/[,;]+/)
      .map(s => s.trim())
      .filter(s => s.length > 0);
  }

  function syncStackToInput() {
    input.value = currentStack.join(', ');
    updateDisplay();
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function updateDisplay() {
    const val = input.value;
    charCount.textContent = val.length;
    if (val.length > 255) {
      charCount.classList.add('text-red-600', 'font-bold');
    } else {
      charCount.classList.remove('text-red-600', 'font-bold');
    }

    stackCount.textContent = currentStack.length;
    if (clearBtn) {
      clearBtn.classList.toggle('hidden', currentStack.length === 0);
    }

    stackedContainer.innerHTML = '';
    if (currentStack.length === 0) {
      if (emptyMsg) stackedContainer.appendChild(emptyMsg);
    } else {
      currentStack.forEach((item, index) => {
        const chip = document.createElement('span');
        chip.className = 'inline-flex items-center gap-1.5 rounded-full bg-forest text-white px-2.5 py-1 text-xs font-medium shadow-sm';
        chip.innerHTML = `<span>${escapeHtml(item)}</span><button type="button" class="remove-chip-btn hover:text-red-200 font-bold ml-1 text-sm leading-none" data-index="${index}" aria-label="Remove ${escapeHtml(item)}">&times;</button>`;
        stackedContainer.appendChild(chip);
      });
    }

    presetPills.forEach(pill => {
      const text = pill.dataset.preset;
      const isActive = currentStack.some(s => s.toLowerCase() === text.toLowerCase());
      const icon = pill.querySelector('.preset-icon');
      if (isActive) {
        pill.classList.remove('bg-mist', 'text-gray-800', 'border-gray-200');
        pill.classList.add('bg-forest-tint', 'text-forest', 'border-forest', 'font-semibold', 'ring-1', 'ring-forest');
        if (icon) icon.textContent = '✓';
      } else {
        pill.classList.remove('bg-forest-tint', 'text-forest', 'border-forest', 'font-semibold', 'ring-1', 'ring-forest');
        pill.classList.add('bg-mist', 'text-gray-800', 'border-gray-200');
        if (icon) icon.textContent = '+';
      }
    });
  }

  stackedContainer.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-chip-btn');
    if (btn) {
      const idx = parseInt(btn.dataset.index, 10);
      currentStack.splice(idx, 1);
      syncStackToInput();
    }
  });

  presetPills.forEach(pill => {
    pill.addEventListener('click', () => {
      const text = pill.dataset.preset;
      const existingIdx = currentStack.findIndex(s => s.toLowerCase() === text.toLowerCase());
      if (existingIdx >= 0) {
        currentStack.splice(existingIdx, 1);
      } else {
        currentStack.push(text);
      }
      syncStackToInput();
    });
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      currentStack = [];
      syncStackToInput();
    });
  }

  function addCustom() {
    const val = customInput.value.trim();
    if (!val) return;
    if (!currentStack.some(s => s.toLowerCase() === val.toLowerCase())) {
      currentStack.push(val);
      syncStackToInput();
    }
    customInput.value = '';
  }

  if (customAddBtn) {
    customAddBtn.addEventListener('click', addCustom);
  }
  if (customInput) {
    customInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        addCustom();
      }
    });
  }

  input.addEventListener('input', () => {
    currentStack = parseFromText(input.value);
    updateDisplay();
  });

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase().trim();
      presetPills.forEach(pill => {
        const match = !q || pill.dataset.preset.toLowerCase().includes(q);
        pill.style.display = match ? 'inline-flex' : 'none';
      });
      groupDivs.forEach(group => {
        const visiblePills = group.querySelectorAll('.preset-pill:not([style*="display: none"])');
        group.style.display = visiblePills.length > 0 ? 'block' : 'none';
      });
    });
  }

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      tabBtns.forEach(b => {
        b.classList.remove('bg-forest', 'text-white');
        b.classList.add('bg-gray-100', 'text-gray-700');
      });
      btn.classList.add('bg-forest', 'text-white');
      btn.classList.remove('bg-gray-100', 'text-gray-700');

      const targetTab = btn.dataset.tab;
      groupDivs.forEach(group => {
        if (targetTab === 'all' || group.dataset.group === targetTab) {
          group.style.display = 'block';
        } else {
          group.style.display = 'none';
        }
      });
    });
  });

  currentStack = parseFromText(input.value);
  updateDisplay();
});
</script>
@endsection
