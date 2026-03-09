@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200">
                        <th
                            class="px-6 py-4 text-base font-bold text-orion-blue uppercase tracking-wider border-r border-slate-300 w-16 text-center">
                            SL
                        </th>
                        <th
                            class="px-6 py-4 text-base font-bold text-orion-blue uppercase tracking-wider border-r border-slate-300 w-80">
                            Name, Degree & Session
                        </th>
                        <th
                            class="px-6 py-4 text-base font-bold text-orion-blue uppercase tracking-wider border-r border-slate-300">
                            Name of Medical College
                        </th>
                        <th class="text-base font-bold text-orion-blue uppercase tracking-wider w-0 text-center">
                            Photo
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse($items as $index => $item)
                        <tr class="transition-colors duration-200 tr-slide-in" style="animation-delay: {{ $index * 0.05 }}s">

                            <td class="px-6 py-4 text-center text-slate-400 border-r border-slate-300 align-top">
                                {{ $items->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4 border-r border-slate-300 align-top">
                                <div class="font-semibold text-slate-700 text-base">
                                    {{ $item->name }}
                                </div>
                                <div class="mt-2 space-y-1">
                                    <div class="text-base text-slate-500 flex items-center gap-1">
                                        <span class="text-slate-400">Degree:</span>
                                        {{ $item->degree }}
                                    </div>
                                    @if($item->session)
                                        <div class="text-base text-slate-500 flex items-center gap-1">
                                            <span class="text-slate-400">Session:</span>
                                            {{ str_replace('Session: ', '', $item->session) }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 border-r border-slate-300 align-top">
                                <div class="text-slate-700 leading-relaxed text-base">
                                    {{ $item->medical_college }}
                                </div>
                            </td>

                            <td class="p-2 align-top">
                                <div class="flex justify-center">
                                    <div class="relative w-20 aspect-9/11 shimmer">
                                        <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                                            alt="{{ $item->name }}" class="product-image w-full h-full object-cover">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-users-slash text-slate-200 text-5xl mb-4"></i>
                                    <p class="text-slate-400 font-medium">No recipients found at this time.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>
        {{ $items->links() }}
    </div>
@endsection