@php
$color = 'yellow-400';
switch ($value) {
case "pending":
$color = 'yellow-400';
break;
case "ready":
$color = 'blue-400';
break;
case "review":
$color = 'yellow-600';
break;
case "failed":
$color = 'red-400';
break;
case "successful":
$color = 'green-400';
break;
case "delivered":
$color = 'green-400';
break;
case "enroute":
$color = 'blue-600';
break;
}
@endphp

<div class="w-24 flex items-center justify-center px-2 py-1 m-1 font-medium bg-{{ $color }} border border-{{ $color }} rounded-full text-white">
    <div class="flex-initial max-w-full text-xs font-normal leading-none">{{ ucfirst($value) ?? '' }}</div>
</div>
