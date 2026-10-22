@php
  $d = $o->daysLeft();
  $label = $d < 0 ? 'Closed' : ($d === 0 ? 'Closes today' : ($d === 1 ? 'Closes tomorrow' : "{$d} days left"));
@endphp
{{ $o->deadline->format('M j, Y') }} ({{ $label }})
