@extends('admin.layouts.app')
@section('title', 'Connect Queries')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Connect Queries</h1>
                <p class="text-xs text-slate-400">Monitor and manage messeages from users</p>
            </div>
            <div>
                <span class="text-xs font-semibold text-amber-500 tracking-wide">
                    Total Received:
                </span>
                <span class="text-xs font-semibold text-red-500">
                    {{ $groupedQueries->flatten()->count() }}
                </span>
            </div>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div class="space-y-4">
                @forelse($groupedQueries as $date => $items)
                    <div
                        class="p-5 rounded-3xl {{ $loop->index % 2 == 0 ? 'bg-red-50/50 border-red-100' : 'bg-green-50/50 border-green-100' }} border space-y-4">

                        <div class="flex items-center gap-2 ml-1">
                            <span
                                class="text-xl font-bold uppercase tracking-widest {{ $loop->index % 2 == 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ date('d F, Y', strtotime($date)) }}
                            </span>
                        </div>

                        @foreach($items as $q)
                            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">

                                <div class="p-4 flex justify-between items-center border-b border-slate-200">
                                    <div
                                        class="font-medium capitalize text-base text-admin-blue pr-4 text-justify flex items-center gap-2">

                                        <span
                                            class="inline-block bg-admin-blue text-white text-xs font-bold px-2 py-1 rounded-md -skew-x-12">
                                            <span class="inline-block skew-x-12">#{{ $q->id }}</span>
                                        </span>

                                        <span>{{ $q->subject }}</span>

                                    </div>

                                    <div class="text-right">
                                        <div class="text-xs opacity-80">Date</div>
                                        <div class="text-xs text-admin-blue font-semibold">
                                            {{ $q->date->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 space-y-3">

                                    <div class="flex items-center justify-between">
                                        <div class="flex">
                                            <span class="w-32 font-semibold text-slate-500 text-sm">Name:</span>
                                            <span class="text-sm text-slate-700">{{ $q->name }}</span>
                                        </div>

                                        <button class="btn-danger w-0 h-0 p-0 rounded-xl flex items-center justify-center"
                                            onclick="deleteQuery({{ $q->id }})">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </div>

                                    <div class="flex">
                                        <span class="w-32 font-semibold text-slate-500 text-sm">Email:</span>
                                        <a href="mailto:{{ $q->email }}" class="text-sm text-admin-blue">
                                            {{ $q->email }}
                                        </a>
                                    </div>

                                    <div class="flex">
                                        <span class="w-32 font-semibold text-slate-500 text-sm">Phone:</span>
                                        <span class="text-sm text-slate-700">{{ $q->phone }}</span>
                                    </div>

                                    <div>
                                        <span class="font-semibold text-slate-500 text-sm block mb-1">Message:</span>
                                        <div class="bg-slate-50 rounded-lg p-3 text-sm text-slate-600">
                                            {{ $q->message }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                        <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Queries Found</h2>
                        <p class="text-xs">System is currently clear of any kind of queries.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection