@extends('layouts.app')
@section('title', $job->title)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('career.index') }}" class="hover:text-orion-blue">Careers</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="capitalize line-clamp-1">{{ $job->title }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

        <article class="lg:col-span-8">

            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

                <div class="px-8 py-6 border-b border-slate-200">

                    <div class="text-2xl font-bold text-orion-blue leading-tight capitalize mb-4">
                        {{ $job->title }}
                    </div>

                    <div class="flex flex-wrap gap-3 text-sm font-semibold mb-3">

                        @if($job->on_from && $job->on_to)
                            <span class="px-4 py-2 rounded-full bg-slate-50">
                                Schedule:
                                <span class="text-rose-500">
                                    {{ $job->on_from->format('d M, Y') }} - {{ $job->on_to->format('d M, Y') }}
                                </span>
                            </span>
                        @elseif($job->on_from || $job->on_to)
                            <span class="px-4 py-2 rounded-full bg-slate-50">
                                Deadline:
                                <span class="text-rose-500">
                                    {{ optional($job->on_to ?? $job->on_from)->format('d M, Y') }}
                                </span>
                            </span>
                        @else
                            <span class="px-4 py-2 rounded-full bg-slate-50 text-emerald-600">
                                Open Until Filled
                            </span>
                        @endif

                        <span class="px-4 py-2 rounded-full bg-slate-50 text-slate-500">
                            {{ $job->job_type }}
                        </span>

                        <span class="px-4 py-2 rounded-full bg-slate-50 text-slate-500">
                            <span class="flex items-center gap-2">
                                @if($job->apply_type === 'Online')
                                    Apply Online
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                @else
                                    Apply Offline
                                @endif
                            </span>
                        </span>

                    </div>

                    @if($job->location)
                        <div class="text-sm font-semibold">
                            <span class="px-4 py-2 rounded-full bg-slate-50 text-slate-500 inline-flex items-center">
                                <i class="fas fa-location-dot mr-2 text-orion-blue"></i>
                                {{ $job->location }}
                            </span>
                        </div>
                    @endif

                </div>

                @if($job->converted_images)
                    <div class="space-y-1 bg-slate-100">
                        @foreach($job->converted_images as $img)
                            <div class="overflow-hidden shimmer">
                                <img src="{{ url('career/' . basename($img)) }}" width="{{ $job->image_width }}"
                                    height="{{ $job->image_height }}" class="w-full h-auto object-contain"
                                    onload="this.parentElement.classList.remove('shimmer')">
                            </div>
                        @endforeach
                    </div>
                @elseif($job->file_path && !str_ends_with($job->file_path, '.pdf'))
                    <div class="overflow-hidden shimmer">
                        <img src="{{ url('career/' . basename($job->file_path)) }}" width="{{ $job->image_width }}"
                            height="{{ $job->image_height }}" class="w-full h-auto object-contain"
                            onload="this.parentElement.classList.remove('shimmer')">
                    </div>
                @endif

                @if(!blank($job->description))
                    <div class="px-10 py-8 border-t border-slate-200">
                        <div class="prose max-w-none text-slate-700 leading-relaxed">
                            {!! $job->description !!}
                        </div>
                    </div>
                @endif

                @if($job->apply_type === 'Online')
                    <div class="bg-slate-800 text-white p-6 text-center">
                        <h3 class="text-lg font-bold mb-2">
                            Ready to Apply?
                        </h3>
                        <p class="text-slate-300 text-base mb-6">
                            Submit your CV in PDF format to start your journey with us.
                        </p>
                        <button onclick="openApplyModal()"
                            class="px-6 py-2 rounded-xl bg-emerald-600 font-bold hover:bg-white hover:text-slate-900 duration-200 cursor-pointer">
                            Apply Now
                        </button>
                    </div>
                @endif
            </div>
        </article>

        <aside class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden sticky top-27.5">

                <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                    <span class="text-xl font-bold text-slate-700 flex items-center gap-3">
                        <span class="flex-none w-1 h-5 bg-orion-blue rounded-full"></span>
                        Latest Openings
                    </span>
                </div>

                <div class="divide-y divide-slate-200">
                    @php
                        $latestJobs = \App\Models\Career::where('is_active', 1)
                            ->where('id', '!=', $job->id)
                            ->orderBy('order')
                            ->take(5)
                            ->get();
                    @endphp

                    @foreach($latestJobs as $latest)
                        <a href="{{ route('career.show', $latest->slug) }}"
                            class="group block p-5 hover:bg-blue-50 transition-colors">

                            <span
                                class="text-base font-semibold text-slate-700 capitalize line-clamp-2 group-hover:text-orion-blue transition-colors leading-snug">
                                {{ $latest->title }}
                            </span>

                            <div class="mt-2 text-sm text-slate-400 tracking-wider">
                                {{ $latest->job_type }} ||
                                {{ $job->apply_type === 'Online' ? 'Apply Online' : 'Apply Offline' }}

                                @if($latest->location)
                                    || {{ $latest->location }}
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="p-4 bg-slate-50/50 border-t border-slate-200">
                    <a href="{{ route('career.index') }}"
                        class="flex items-center justify-center gap-2 w-full py-3 bg-white text-orion-blue text-xs font-bold uppercase tracking-wider rounded-xl border border-slate-200 hover:border-orion-blue hover:bg-orion-blue hover:text-white transition-all duration-300">
                        View All Jobs
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>

            </div>
        </aside>

    </div>

    <div id="applyModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 p-4">

        <div id="applyModalContent"
            class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl relative transform translate-y-8 opacity-0 transition-all duration-300 ease-out">

            <button onclick="closeApplyModal()"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors cursor-pointer">
                <i class="fas fa-times"></i>
            </button>

            <span class="text-2xl font-bold text-orion-blue">
                Submit Application
            </span>
            <p class="text-slate-500 text-sm mt-1 mb-6">
                Upload your CV (PDF format only).
            </p>

            <form id="applyForm" onsubmit="submitApplication(event, '{{ route('career.apply', $job->slug) }}')">
                @csrf
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center bg-slate-50 hover:bg-slate-100 hover:border-orion-blue transition-colors cursor-pointer mb-6"
                    onclick="document.getElementById('cvInput').click()">
                    <i class="fas fa-file-pdf text-4xl text-slate-300 mb-3"></i>
                    <p id="fileName" class="text-sm font-semibold text-slate-500">
                        Click to select PDF file
                    </p>
                    <input type="file" id="cvInput" accept=".pdf" class="hidden" required
                        onchange="document.getElementById('fileName').innerText = this.files[0]?.name || 'Click to select PDF file'">
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full h-12 rounded-xl bg-emerald-600 text-white font-bold hover:bg-slate-800 transition-colors flex items-center justify-center cursor-pointer">
                    Upload
                </button>
            </form>
        </div>
    </div>

    <div id="successModal"
        class="hidden fixed inset-0 z-60 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 p-4">

        <div id="successModalContent"
            class="bg-white rounded-3xl max-w-sm w-full p-8 shadow-2xl text-center transform translate-y-8 opacity-0 transition-all duration-300 ease-out">

            <div
                class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="fas fa-check"></i>
            </div>

            <span class="text-2xl font-bold text-orion-blue">
                Application Sent!
            </span>

            <p class="text-slate-500 text-sm mb-6">
                Your CV has been successfully uploaded.
            </p>

            <button onclick="closeSuccessModal()"
                class="w-full h-12 rounded-xl bg-red-50 text-red-500 font-bold border border-red-200 hover:bg-red-100 transition-colors flex items-center justify-center cursor-pointer">
                Close
            </button>
        </div>
    </div>
@endsection