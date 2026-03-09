@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-12 max-w-350 mx-auto">

        <div class="lg:col-span-6">
            <div class="bg-white rounded-xl border border-slate-200 p-8 md:p-12">
                <div class="flex justify-between items-center mb-10 border-b border-slate-100 pb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Product Complaint Form</h2>
                    <div class="text-right">
                        <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest">Raising
                            Date</span>
                        <span class="text-orion-blue font-bold text-lg">{{ $raisingDate }}</span>
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
                                Complaint Submitted Successfully
                            </h3>
                            <p class="text-center text-slate-600">
                                Thank you for reporting the issue. Our team will review it shortly.
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

                <form action="{{ route('complaint.submit') }}" method="POST" class="space-y-10">
                    @csrf

                    <div>
                        <h3
                            class="text-orion-blue font-bold uppercase text-sm tracking-widest mb-6 flex items-center gap-3">
                            SECTION A: Product Identification
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Product Name</label>
                                <input type="text" name="product_name" required
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Batch Number</label>
                                <input type="text" name="batch_number" required
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Manufacturing
                                    Date</label>
                                <input type="date" name="mfg_date" required
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Expiry Date</label>
                                <input type="date" name="exp_date" required
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Strength</label>
                                <input type="text" name="strength"
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Dosage Form</label>
                                <input type="text" name="dosage_form"
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3
                            class="text-orion-blue font-bold uppercase text-sm tracking-widest mb-6 flex items-center gap-3">
                            SECTION B: Nature of Complaint
                        </h3>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Type of Complaint</label>
                                <input type="text" name="complaint_type"
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Description of
                                    Complaint</label>
                                <textarea name="complaint_description" rows="4"
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3
                            class="text-orion-blue font-bold uppercase text-sm tracking-widest mb-6 flex items-center gap-3">
                            SECTION C: Complainant Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Name of
                                    Complainant</label>
                                <input type="text" name="complainant_name" required
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Contact Number</label>
                                <input type="text" name="contact_number" required
                                    class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Address</label>
                            <textarea name="address" rows="3"
                                class="w-full px-3 py-2 bg-slate-50  border border-slate-200 rounded-lg transition-all resize-none"></textarea>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-orion-blue text-white font-bold uppercase rounded-lg hover:bg-blue-900 cursor-pointer transition-all">
                        Submit Complaint
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky top-27.5 space-y-6">
                <div class="bg-white border border-slate-200 rounded-xl p-6">

                    <div class="grid grid-cols-[auto_max-content] gap-x-5 gap-y-4 items-center">

                        <div></div>
                        <div class="text-slate-500 text-base font-semibold text-center max-w-xs">
                            For any inquiry or to report Product Complaint, please contact us:
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="bg-blue-50 text-orion-blue w-10 h-10 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                        </div>
                        <div
                            class="bg-orion-blue text-white rounded-lg px-4 py-3 space-y-1 text-center flex flex-col items-center justify-center">
                            <div class="text-3xl font-bold">+88 01897 65 31 31</div>
                            <div class="text-base text-slate-300">10:00 - 18:00</div>
                            <div class="text-xs text-slate-300">
                                (Except Friday and Government Holidays)
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="bg-blue-50 text-orion-blue w-10 h-10 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                        </div>
                        <div
                            class="bg-orion-blue text-white rounded-lg px-4 py-3 text-base font-medium text-center flex items-center justify-center">
                            pc@orion-group.net
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection