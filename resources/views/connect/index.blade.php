@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-12">

        <div class="lg:col-span-6">
            <div class="bg-white rounded-xl border border-slate-200 p-8 md:p-12">
                <div class="flex justify-between items-center mb-10 border-b border-slate-100 pb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Have Any Query?</h2>
                    <div class="text-right">
                        <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest">Date</span>
                        <span class="text-orion-blue font-bold text-lg">{{ $date }}</span>
                    </div>
                </div>

                @if(session('success'))
                    <div id="successModal"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300">

                        <div
                            class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 relative transform translate-y-8 opacity-0 transition-all duration-300 ease-out">

                            <div class="flex justify-center mb-4">
                                <div
                                    class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                    <i class="fa-solid fa-check text-3xl"></i>
                                </div>
                            </div>

                            <h3 class="text-xl font-bold text-center text-slate-800 mb-2">
                                Query Submitted Successfully
                            </h3>
                            <p class="text-center text-slate-600">
                                Thank you for contacting us. Our team will contact with you shortly.
                            </p>

                            <div class="mt-6 text-center">
                                <button onclick="closeSuccessModal()"
                                    class="px-6 py-3 bg-orion-blue text-white rounded-lg font-bold hover:bg-blue-900 cursor-pointer transition">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('connect.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Name</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Phone</label>
                        <input type="text" name="phone" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Subject</label>
                        <input type="text" name="subject" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Message</label>
                        <textarea name="message" rows="5" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl resize-none"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-orion-blue text-white font-bold uppercase rounded-lg hover:bg-blue-900 transition-all cursor-pointer">
                        Submit
                    </button>

                </form>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky top-27.5 space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <div class="aspect-square w-full relative map-wrapper">
                        @if($footer->map_url)

                            <div
                                class="map-loader absolute inset-0 flex items-center justify-center bg-white transition-opacity duration-500">
                                <div class="h-60 flex items-center justify-center">
                                    <x-map-loader />
                                </div>
                            </div>

                            <iframe data-src="{{ $footer->map_url }}" width="100%" height="100%" style="border:0;"
                                class="map-frame opacity-0 transition-opacity duration-700" loading="lazy">
                            </iframe>

                        @else
                            <div class="w-full h-full bg-stone-100 flex items-center justify-center text-slate-500 text-sm">
                                Map not configured
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection