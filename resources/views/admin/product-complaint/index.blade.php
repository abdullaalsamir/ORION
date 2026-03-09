@extends('admin.layouts.app')
@section('title', 'Product Complaints')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>Product Complaints</h1>
                <p class="text-xs text-slate-400">Monitor and manage quality assurance reports from
                    customers</p>
            </div>
            <div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Received:
                    {{ $groupedComplaints->flatten()->count() }}</span>
            </div>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar">
            <div class="space-y-4">
                @forelse($groupedComplaints as $date => $items)
                    <div
                        class="p-5 rounded-3xl {{ $loop->index % 2 == 0 ? 'bg-red-50/50 border-red-100' : 'bg-green-50/50 border-green-100' }} border space-y-4">

                        <div class="flex items-center gap-2 ml-1">
                            <span
                                class="text-xl font-black uppercase tracking-[0.2em] {{ $loop->index % 2 == 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ date('d F, Y', strtotime($date)) }}
                            </span>
                        </div>

                        @foreach($items as $c)
                            <div
                                class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-admin-blue transition-all flex flex-col gap-5">

                                <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                                    <div class="flex flex-col gap-0.5">
                                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Complaint
                                            From</label>
                                        <h3 class="font-bold text-slate-700 text-base">{{ $c->complainant_name }}</h3>
                                        <span class="text-xs text-admin-blue font-semibold">{{ $c->contact_number }}</span>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="badge badge-purple text-[8px]!">{{ $c->complaint_type }}</span>
                                        <span
                                            class="text-[10px] font-bold text-slate-400 uppercase">{{ $c->complaint_date->format('h:i A') }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">Product
                                            Name</label>
                                        <p class="text-xs font-bold text-slate-700">{{ $c->product_name }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">Batch
                                            Number</label>
                                        <p class="text-xs font-bold text-slate-700">{{ $c->batch_number }}</p>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">MFG
                                            Date</label>
                                        <p class="text-xs font-bold text-slate-700">{{ $c->mfg_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">EXP
                                            Date</label>
                                        <p class="text-xs font-bold text-red-500">{{ $c->exp_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <label
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Detailed
                                        Description</label>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $c->complaint_description }}</p>
                                </div>

                                <div class="flex items-end justify-between gap-8 pt-1">
                                    <div class="flex flex-col gap-1 min-w-0">
                                        <label class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Customer
                                            Address</label>
                                        <p class="text-[11px] text-slate-500 leading-snug truncate">{{ $c->address }}</p>
                                    </div>
                                    <button
                                        class="btn-danger w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0 hover:bg-red-500 hover:text-white transition-all"
                                        onclick="deleteComplaint({{ $c->id }})">
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                        <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                        <h2 class="text-slate-400!">No Complaints Found</h2>
                        <p class="text-xs">System is currently clear of any quality reports.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection