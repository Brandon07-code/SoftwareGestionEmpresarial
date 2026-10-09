@props(['dark' => null])

@php
    $titleColor = $dark === true ? 'text-white' : ($dark === false ? 'text-slate-900' : 'text-slate-900 dark:text-white');
    $subtitleColor = $dark === true ? 'text-slate-400' : ($dark === false ? 'text-slate-500' : 'text-slate-500 dark:text-slate-400');
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 shrink-0']) }}>
    <!-- Isotipo Vectorial Tecnológico -->
    <div class="relative w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 p-[1.5px] shadow-md shadow-indigo-500/20 flex items-center justify-center shrink-0 group">
        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center overflow-hidden">
            <svg class="w-5 h-5 text-indigo-400 transition-transform duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="url(#logo-grad-1)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 22V12" stroke="url(#logo-grad-2)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 7L12 12L3 7" stroke="url(#logo-grad-1)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="2.2" fill="#38BDF8"/>
                <defs>
                    <linearGradient id="logo-grad-1" x1="3" y1="2" x2="21" y2="22" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#38BDF8"/>
                        <stop offset="0.5" stop-color="#6366F1"/>
                        <stop offset="1" stop-color="#818CF8"/>
                    </linearGradient>
                    <linearGradient id="logo-grad-2" x1="12" y1="12" x2="12" y2="22" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#38BDF8"/>
                        <stop offset="1" stop-color="#4F46E5"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </div>
    <div class="flex flex-col leading-tight shrink-0">
        <div class="flex items-center gap-1.5">
            <span class="font-black text-[15px] tracking-tight {{ $titleColor }}">NEXUS</span>
            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black tracking-wider bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xs">ERP</span>
        </div>
        <span class="text-[9px] uppercase tracking-widest font-semibold hidden xl:inline whitespace-nowrap {{ $subtitleColor }}">Gestión & Operaciones</span>
    </div>
</div>

