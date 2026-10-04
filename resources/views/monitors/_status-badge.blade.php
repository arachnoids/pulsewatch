@php $s = $status; @endphp
@if ($s === 'up')
    <span class="px-2 py-1 rounded text-white text-xs font-semibold bg-green-600">UP</span>
@elseif ($s === 'down')
    <span class="px-2 py-1 rounded text-white text-xs font-semibold bg-red-600">DOWN</span>
@else
    <span class="px-2 py-1 rounded text-white text-xs font-semibold bg-yellow-500">DEGRADED</span>
@endif