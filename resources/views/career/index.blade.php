@extends('layouts.app')
@section('title', 'Career')

@section('content')
    <div>
        <span class="text-xl text-justify font-bold text-orion-blue leading-relaxed">
            Build a Legacy of Excellence. Shape the Future of Health.
        </span>

        <p class="text-slate-500 text-base text-justify pt-2">
            At Orion Pharma Ltd., we believe that medicine is only as powerful as the minds behind it. For over four
            decades,
            our journey has been defined by a single, unwavering ethos: In Search of Excellence. But excellence isn’t just a
            standard we maintain in our advanced facilities; it is the heartbeat of our people and the dedication they bring
            to
            work every morning. It lives in the passion of our 2,100+ innovators, pharmacists, and specialists.
        </p>

        <p class="text-slate-500 text-base text-justify pt-4">
            When you join Orion, you aren’t just starting a job; you are joining a mission to deliver hope across Bangladesh
            and
            the world. We provide the platform, the global-standard technology, and the culture of care. You bring
            brilliance.
            Let’s redefine healthcare, together.
        </p>
    </div>

    <div class="mt-8">

        @if($jobs->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
                <div class="text-5xl text-slate-300 mb-4">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="text-lg font-semibold text-slate-400">
                    No Open Positions
                </span>
                <p class="text-base text-slate-400">
                    We currently have no openings. Please check back later.
                </p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($jobs as $job)
                    <a href="{{ route('career.show', $job->slug) }}"
                        class="group bg-white border border-slate-200 rounded-2xl px-8 py-6 flex items-center justify-between transition-all duration-300 hover:border-orion-blue">
                        <div class="flex-1 min-w-0">

                            <h3
                                class="text-xl font-semibold text-slate-900 capitalize group-hover:text-orion-blue transition-all duration-300 line-clamp-2">
                                {{ $job->title }}
                            </h3>

                            <div class="mt-4 flex flex-wrap gap-3 text-sm font-semibold">

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

                                @if($job->location)
                                    <span class="px-4 py-2 rounded-full bg-slate-50 text-slate-500">
                                        <i class="fas fa-location-dot mr-1 text-orion-blue"></i>
                                        {{ $job->location }}
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

                        </div>

                        <div class="shrink-0 pl-6 text-slate-300 group-hover:text-orion-blue transition-all duration-300">
                            <i class="fas fa-chevron-right text-base"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection