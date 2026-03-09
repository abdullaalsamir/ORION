@extends('admin.layouts.app')

@section('title', 'Detailed Dashboard')

@section('content')
    <div class="admin-card">

        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>System Analytics Overview</h1>
                <p class="text-xs text-slate-400">
                    Detailed breakdown of all modules and their active statuses.
                </p>
            </div>

            <button onclick="window.location.reload()"
                class="bg-slate-50 border border-slate-200 text-slate-600 hover:bg-admin-blue hover:text-white hover:border-admin-blue transition-all px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 cursor-pointer">
                <i class="fas fa-sync-alt text-xs"></i> Refresh
            </button>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar p-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 items-stretch">

                @foreach($cards as $card)

                    <div
                        class="bg-white rounded-2xl border border-slate-200 p-5 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">

                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                    {{ $card['title'] }}
                                </h3>

                                <div class="text-3xl font-bold text-slate-800 mt-1">
                                    {{ number_format($card['total']) }}
                                </div>
                            </div>

                            <div
                                class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 text-admin-blue flex items-center justify-center text-lg shrink-0">
                                <i class="fas {{ $card['icon'] }}"></i>
                            </div>
                        </div>

                        @if(!isset($card['no_status']))
                            <div class="flex items-center gap-4 text-xs mb-2 pb-2">
                                <span class="text-emerald-500 flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i>
                                    {{ number_format($card['active']) }} Active
                                </span>

                                <span class="text-rose-400 flex items-center gap-1">
                                    <i class="fas fa-times-circle"></i>
                                    {{ number_format($card['inactive']) }} Inactive
                                </span>
                            </div>
                        @else
                            <div class="flex items-center gap-4 text-xs mb-2 pb-2">
                                <span class="text-amber-500 flex items-center gap-1">
                                    <i class="fas fa-inbox"></i>
                                    Total Received
                                </span>
                            </div>
                        @endif


                        <div class="mt-auto">

                            @if(isset($card['subs']) && count($card['subs']) > 0)

                                @php
                                    $colClass = count($card['subs']) === 3 ? 'grid-cols-3' :
                                        (count($card['subs']) === 2 ? 'grid-cols-2' : 'grid-cols-1');
                                @endphp

                                <div class="bg-slate-50 rounded-xl p-1 grid {{ $colClass }} gap-2 border border-slate-200">

                                    @foreach($card['subs'] as $sub)

                                        <div
                                            class="text-center p-2 bg-white rounded-lg border border-slate-200 flex flex-col justify-center">

                                            <div
                                                class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1 leading-tight">
                                                {{ $sub['label'] }}
                                            </div>

                                            <div class="text-sm font-bold text-admin-blue">
                                                {{ number_format($sub['value']) }}
                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <div class="bg-transparent rounded-xl p-2 border border-transparent h-17"></div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>
@endsection