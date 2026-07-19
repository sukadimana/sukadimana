@php
    $params = $params ?? [];
    $active = request()->routeIs($route)
        && array_intersect_key($params, request()->route()?->parameters() ?? []) == $params;
@endphp
<a href="{{ route($route, $params) }}"
   class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-lg mb-1
    {{ $active ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-blue-600' }}"
>
    <x-icon :name="$icon" class="w-5 h-5" />
    <span>{{ $label }}</span>
</a>
