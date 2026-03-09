@extends('admin.layouts.app')

@section('title', 'System Settings')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="flex flex-col">
                <h1>System Settings</h1>
                <p class="text-xs text-slate-400">Manage your site identity and administrative credentials.</p>
            </div>
        </div>

        <div class="admin-card-body bg-slate-50/20 custom-scrollbar p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="bg-white rounded-2xl border border-slate-200 flex flex-col relative">

                    @if(session('assetsSuccess'))
                        <div id="successOverlay"
                            class="absolute inset-0 bg-white/70 z-50 flex flex-col items-center justify-center rounded-2xl transition-opacity duration-500 backdrop-blur-xs">
                            <i
                                class="fas fa-check-circle text-5xl text-emerald-500 mb-3 shadow-emerald-500/20 drop-shadow-lg"></i>
                            <span class="text-slate-700 font-extrabold text-lg tracking-tight">
                                Assets Updated
                            </span>
                        </div>
                    @endif

                    <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
                        <h1 class="text-lg! mb-0! flex items-center gap-2">Site Identity</h1>
                    </div>

                    <div class="p-6">
                        <form action="{{ url('admin/settings/update-assets') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-2 gap-6">

                                <div>
                                    <label class="text-[11px] text-slate-400 mb-2 block ml-1">
                                        <span class="uppercase font-bold">Main Logo</span> (logo.svg)
                                    </label>

                                    <input type="file" name="logo" id="logoInput" accept=".svg" class="hidden"
                                        onchange="updateSvgPreview(this, 'logoImg')">

                                    <div class="relative group cursor-pointer w-full h-53 bg-slate-50 rounded-xl border border-slate-200 border-dashed overflow-hidden flex items-center justify-center transition-all hover:border-admin-blue"
                                        onclick="document.getElementById('logoInput').click()">

                                        <img src="{{ asset('logo.svg') }}?t={{ time() }}" id="logoImg"
                                            class="w-2/3 h-2/3 object-contain transition-opacity duration-300">

                                        <div
                                            class="absolute inset-0 bg-admin-blue/80 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                            <span class="text-white font-bold text-[11px] uppercase tracking-widest">
                                                Replace Main Logo
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[11px] text-slate-400 mb-2 block ml-1">
                                        <span class="uppercase font-bold">Favicon</span> (favicon.svg)
                                    </label>

                                    <input type="file" name="favicon" id="faviconInput" accept=".svg" class="hidden"
                                        onchange="updateSvgPreview(this, 'faviconImg')">

                                    <div class="relative group cursor-pointer w-full h-53 bg-slate-50 rounded-xl border border-slate-200 border-dashed overflow-hidden flex items-center justify-center transition-all hover:border-admin-blue"
                                        onclick="document.getElementById('faviconInput').click()">

                                        <img src="{{ asset('favicon.svg') }}?t={{ time() }}" id="faviconImg"
                                            class="w-16 h-16 object-contain transition-opacity duration-300">

                                        <div
                                            class="absolute inset-0 bg-admin-blue/80 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                            <span class="text-white font-bold text-[11px] uppercase tracking-widest">
                                                Replace Favicon
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn-primary flex items-center justify-center w-full h-11!">
                                <i class="fas fa-upload mr-2"></i> Update Assets
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 flex flex-col relative">

                    @if(session('passwordSuccess'))
                        <div id="successOverlay"
                            class="absolute inset-0 bg-white/70 z-50 flex flex-col items-center justify-center rounded-2xl transition-opacity duration-500 backdrop-blur-xs">
                            <i
                                class="fas fa-check-circle text-5xl text-emerald-500 mb-3 shadow-emerald-500/20 drop-shadow-lg"></i>
                            <span class="text-slate-700 font-extrabold text-lg tracking-tight">Password Updated</span>
                        </div>
                    @endif

                    <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
                        <h1 class="text-lg! mb-0! flex items-center gap-2">Update Password</h1>
                    </div>

                    <div class="p-6">
                        <form id="passwordForm" action="{{ url('admin/settings/update-credentials') }}" method="POST"
                            class="space-y-5" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Current Password</label>
                                <input type="password" name="current_password" id="currentPassword" required
                                    class="input-field w-full @if($errors->has('current_password')) border-red-500 bg-red-50 shake @endif"
                                    placeholder="••••••••">
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">New Password</label>

                                <input type="password" name="password" id="newPassword" required minlength="8"
                                    class="input-field w-full transition-colors duration-200" placeholder="••••••••">

                                <div id="passwordStrength" class="mt-1">
                                    <div class="flex items-center gap-2">

                                        <div class="flex-1 h-1 bg-slate-200/40 rounded-full overflow-hidden">
                                            <div id="strengthBar"
                                                class="h-full w-0 bg-slate-300/40 transition-all duration-300">
                                            </div>
                                        </div>

                                        <span id="strengthText"
                                            class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                            Strength
                                        </span>

                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Confirm New
                                    Password</label>
                                <input type="password" name="password_confirmation" id="confirmPassword" required
                                    minlength="8" class="input-field w-full transition-colors duration-200"
                                    placeholder="••••••••">
                            </div>

                            <button type="submit" class="btn-primary flex items-center justify-center w-full h-11! mt-2!">
                                <i class="fas fa-lock mr-2"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection