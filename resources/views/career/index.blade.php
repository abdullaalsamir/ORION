@extends('layouts.app')
@section('title', 'Career')

@section('content')
    <div>
        <p class="text-slate-500 text-base text-justify">
            At ORION, we recognize that our people are our greatest competitive advantage. As a leading industrial
            conglomerate, we are firmly committed to maintaining a workplace defined by Equal Employment Opportunity with
            Diversity, Equity, Inclusion, and Belongingness where every individual is empowered to contribute, grow, and
            feel included and connected. Our merit-driven culture ensures that opportunities are accessible, reinforcing
            fairness, transparency, and respect across every level of the organization.
        </p>

        <p class="text-slate-500 text-base text-justify pt-4">
            We proudly embrace the diversity of our workforce which drives stronger ideas, smarter solutions, and more
            innovative outcomes. Through robust and proactive Affirmative Action initiatives, ORION ensures that individuals
            from all backgrounds are supported, represented, and encouraged to excel, fairly. As part of our commitment to
            developing future leaders, ORION has established a strategic management process for High-Potential employees to
            identify exceptional talent and prepare them for critical leadership roles. From early-stage professionals to
            veteran, we offer clearly defined pathways for career progression, enabling our people to build meaningful,
            long-term careers aligned with both personal aspirations and organizational goals.
        </p>

        <p class="text-slate-500 text-base text-justify pt-4">
            We believe that growth is continuous. To meet the evolving demands of global industries, ORION places strong
            emphasis on comprehensive skill development across all business units. Our dynamic reskilling and upskilling
            programs ensure that employees remain future-ready, technologically adept, and equipped to excel in an
            ever-changing environment. Our competitive salary and benefits, career growth opportunities, positive work
            culture, structured learning & development programs, cross-functional collaboration gives every employee an
            environment of well-being.
        </p>

        <p class="text-slate-500 text-base text-justify pt-4">
            Our aspiration as an Employer of Choice—a destination where talent is nurtured, achievements are celebrated, and
            people feel inspired to perform at their highest potential. At ORION, we are committed to offering an
            empowering, inclusive, and engaging workplace where every employee can shape their future with confidence and
            success.
        </p>
    </div>

    <div class="mt-8">

        @if($jobs->isEmpty())
            <div class="rounded-2xl border border-slate-200 p-16 text-center">
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