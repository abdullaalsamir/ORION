<div id="contactModal" class="modal-overlay hidden">
    <div class="modal-content max-w-xl! h-[90vh]! flex flex-col">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h1 class="mb-0!">Edit Contact Info</h1>
            <button type="button" onclick="closeModal('contactModal')" class="btn-icon"><i
                    class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.footer.update') }}" method="POST"
            class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
            @csrf
            <div class="flex flex-col gap-1 relative">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Company Name</label>
                <input type="text" name="company" value="{{ $footer->company }}" required maxlength="50"
                    class="input-field w-full" oninput="updateCount(this, 'c_company', 50)">
                <span id="c_company" class="absolute right-3 top-8 text-[9px] text-slate-300 font-bold">0/50</span>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Address</label>
                <div class="relative"><input type="text" name="address_1" value="{{ $footer->address_1 }}"
                        placeholder="Address Line 1" required maxlength="50" class="input-field w-full mb-2"
                        oninput="updateCount(this, 'c_addr1', 50)"><span id="c_addr1"
                        class="absolute right-3 top-2.5 text-[9px] text-slate-300 font-bold">0/50</span></div>
                <div class="relative"><input type="text" name="address_2" value="{{ $footer->address_2 }}"
                        placeholder="Address Line 2" required maxlength="50" class="input-field w-full mb-2"
                        oninput="updateCount(this, 'c_addr2', 50)"><span id="c_addr2"
                        class="absolute right-3 top-2.5 text-[9px] text-slate-300 font-bold">0/50</span></div>
                <div class="relative"><input type="text" name="address_3" value="{{ $footer->address_3 }}"
                        placeholder="Address Line 3" required maxlength="50" class="input-field w-full"
                        oninput="updateCount(this, 'c_addr3', 50)"><span id="c_addr3"
                        class="absolute right-3 top-2.5 text-[9px] text-slate-300 font-bold">0/50</span></div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Phone</label>
                <div class="relative"><input type="text" name="phone_1" value="{{ $footer->phone_1 }}"
                        placeholder="Phone Number 1" required maxlength="50" class="input-field w-full mb-2"
                        oninput="updateCount(this, 'c_ph1', 50)"><span id="c_ph1"
                        class="absolute right-3 top-2.5 text-[9px] text-slate-300 font-bold">0/50</span></div>
                <div class="relative"><input type="text" name="phone_2" value="{{ $footer->phone_2 }}"
                        placeholder="Phone Number 2" required maxlength="50" class="input-field w-full"
                        oninput="updateCount(this, 'c_ph2', 50)"><span id="c_ph2"
                        class="absolute right-3 top-2.5 text-[9px] text-slate-300 font-bold">0/50</span></div>
            </div>

            <div class="flex flex-col gap-1 relative">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Fax</label>
                <input type="text" name="fax" value="{{ $footer->fax }}" maxlength="50" class="input-field w-full"
                    oninput="updateCount(this, 'c_fax', 50)">
                <span id="c_fax" class="absolute right-3 top-8 text-[9px] text-slate-300 font-bold">0/50</span>
            </div>

            <div class="flex flex-col gap-1 relative">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Email</label>
                <input type="email" name="email" value="{{ $footer->email }}" required maxlength="50"
                    class="input-field w-full" oninput="updateCount(this, 'c_mail', 50)">
                <span id="c_mail" class="absolute right-3 top-8 text-[9px] text-slate-300 font-bold">0/50</span>
            </div>

            <div class="flex justify-end pt-4 sticky bottom-0 bg-white border-t border-slate-50">
                <button type="submit" class="btn-primary h-10">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="mapModal" class="modal-overlay hidden">
    <div class="modal-content max-w-2xl! h-[90vh]! flex flex-col">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h1 class="mb-0!">Map Configuration</h1>
            <button onclick="closeModal('mapModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.footer.update') }}" method="POST" class="flex-1 flex flex-col space-y-6">
            @csrf
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Google Maps Embed URL</label>
                <div class="flex gap-2">
                    <input type="text" name="map_url" id="map_input" value="{{ $footer->map_url }}"
                        placeholder="https://www.google.com/maps/embed?..." required class="input-field flex-1">
                    <button type="button" onclick="fetchFooterMap()" class="btn-primary h-11 px-6 bg-slate-800">Fetch
                        Map</button>
                </div>
            </div>
            <div class="flex-1 rounded-3xl overflow-hidden border border-slate-200 bg-slate-50">
                <iframe id="map_preview" src="{{ $footer->map_url }}" class="w-full h-full" style="border:0;"></iframe>
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-50">
                <button type="submit" id="mapSaveBtn" disabled
                    class="btn-success h-10 opacity-50 cursor-not-allowed">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="qlModal" class="modal-overlay hidden">
    <div class="modal-content max-w-xl! h-[90vh]! flex flex-col">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100">
            <h1 class="mb-0!">Quick Links Slots</h1>
            <button onclick="closeModal('qlModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.footer.update') }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
            @csrf
            <div id="ql-sortable" class="space-y-2 flex-1 overflow-y-auto custom-scrollbar pr-2 pb-4">
                @for($i = 0; $i < 8; $i++)
                    @php $current = $footer->quick_links[$i] ?? null; @endphp
                    <div class="flex items-center gap-1 p-2 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div
                            class="drag-handle w-8 flex justify-center cursor-grab active:cursor-grabbing text-slate-300 hover:text-admin-blue">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <select name="ql_menu_id[]" class="input-field h-11! flex-1 ql-select">
                            <option value="">⁝⁝⁝ None ⁝⁝⁝</option>

                            @foreach($nestedMenus as $m)
                                @php $mIsCat = $m->children->count() > 0; @endphp
                                <option value="{{ $m->id }}" {{ $mIsCat ? 'disabled' : '' }}
                                    class="{{ $mIsCat ? 'text-red-800 font-semibold' : '' }}" {{ ($current && $current['menu_id'] == $m->id) ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>

                                @foreach($m->children as $c)
                                    @php $cIsCat = $c->children->count() > 0; @endphp
                                    <option value="{{ $c->id }}" {{ $cIsCat ? 'disabled' : '' }}
                                        class="{{ $cIsCat ? 'text-red-800 font-semibold' : 'text-slate-500' }}" {{ ($current && $current['menu_id'] == $c->id) ? 'selected' : '' }}>
                                        — {{ $c->name }}
                                    </option>

                                    @foreach($c->children as $sc)
                                        <option value="{{ $sc->id }}" class="text-slate-400" {{ ($current && $current['menu_id'] == $sc->id) ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp; — {{ $sc->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            @endforeach

                            <option value="career" class="text-orion-blue font-bold" {{ ($current && $current['menu_id'] === 'career') ? 'selected' : '' }}>
                                ⁝⁝⁝ Career ⁝⁝⁝
                            </option>

                            <option value="sitemap" class="text-orion-blue font-semibold" {{ ($current && $current['menu_id'] === 'sitemap') ? 'selected' : '' }}>
                                ⁝⁝⁝ Sitemap ⁝⁝⁝
                            </option>
                        </select>
                    </div>
                @endfor
            </div>
            <div class="flex justify-end pt-4 border-t border-slate-50 mt-4">
                <button type="submit" class="btn-primary h-10">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="followModal" class="modal-overlay hidden">
    <div class="modal-content max-w-xl! h-[90vh]! flex flex-col">
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-100 shrink-0">
            <h1 class="mb-0!">Branding & Social</h1>
            <button onclick="closeModal('followModal')" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('admin.footer.update') }}" method="POST"
            class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-6">
            @csrf
            <div class="flex flex-col gap-1 relative">
                <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Footer Brand Description</label>
                <textarea name="follow_us_desc" required maxlength="200"
                    class="input-field w-full h-28 py-4 resize-none"
                    oninput="updateCount(this, 'cnt_desc', 200)">{{ $footer->follow_us_desc }}</textarea>
                <span id="cnt_desc" class="absolute right-4 bottom-3 text-[9px] text-slate-300 font-bold">0/200</span>
            </div>
            <div id="social-sortable" class="space-y-2">
                @foreach($footer->social_links ?? [] as $social)
                    <div class="flex items-center gap-4 p-3 bg-white border border-slate-200 rounded-2xl">
                        <div class="drag-handle w-6 text-center cursor-grab text-slate-300 hover:text-admin-blue"><i
                                class="fas fa-bars"></i></div>
                        <span
                            class="w-20 text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $social['platform'] }}</span>
                        <input type="hidden" name="social_platform[]" value="{{ $social['platform'] }}">
                        <input type="hidden" name="social_icon[]" value="{{ $social['icon'] }}">
                        <input type="text" name="social_url[]" value="{{ $social['url'] }}" placeholder="https://..."
                            class="input-field h-10! flex-1 text-xs! font-bold!">
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end pt-4 sticky bottom-0 bg-white border-t border-slate-50">
                <button type="submit" class="btn-primary h-10">Update</button>
            </div>
        </form>
    </div>
</div>