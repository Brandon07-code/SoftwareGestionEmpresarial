@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-2 lg:px-2.5 xl:px-3 pt-1 border-b-2 border-indigo-500 text-xs xl:text-sm font-semibold leading-5 text-indigo-300 focus:outline-none focus:border-indigo-400 transition duration-150 ease-in-out whitespace-nowrap shrink-0'
            : 'inline-flex items-center px-2 lg:px-2.5 xl:px-3 pt-1 border-b-2 border-transparent text-xs xl:text-sm font-medium leading-5 text-slate-300 hover:text-white hover:border-slate-500 focus:outline-none focus:text-white focus:border-slate-500 transition duration-150 ease-in-out whitespace-nowrap shrink-0';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
