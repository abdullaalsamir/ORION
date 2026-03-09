@if($menu->slug === 'home') @php return; @endphp @endif

<li data-id="{{ $menu->id }}" class="sortable-item tree-item group">
    <div
        class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-2xl hover:border-admin-blue/50 transition-all relative z-10">
        <div class="flex items-center gap-3">
            <div
                class="drag-handle w-8 flex justify-center cursor-grab active:cursor-grabbing p-1.5 text-slate-300 hover:text-admin-blue transition-colors">
                <i class="fas fa-arrows-up-down-left-right"></i>
            </div>

            <div class="w-6 flex justify-center">
                @if($menu->children->count())
                    <button type="button"
                        class="collapse-toggle w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-400 transition-all"
                        data-target="children-{{ $menu->id }}">
                        <i class="fas fa-chevron-right text-[10px] transition-transform"></i>
                    </button>
                @endif
            </div>

            <span class="font-bold text-slate-600 tracking-tight">{{ $menu->name }}</span>
        </div>

        <div class="flex items-center gap-4">
            <span class="badge {{ $menu->is_multifunctional ? 'badge-purple' : 'badge-info' }} text-[9px]!">
                {{ $menu->is_multifunctional ? 'Multifunctional' : 'Functional' }}
            </span>

            <span class="badge {{ $menu->isEffectivelyActive() ? 'badge-success' : 'badge-danger' }} text-[9px]!">
                {{ $menu->isEffectivelyActive() ? 'Active' : 'Inactive' }}
            </span>

            <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                <button type="button" class="btn-icon edit-btn p-1.5!" data-id="{{ $menu->id }}"
                    data-name="{{ $menu->name }}" data-parent="{{ $menu->parent_id }}"
                    data-active="{{ $menu->is_active ? '1' : '0' }}"
                    data-multi="{{ $menu->is_multifunctional ? '1' : '0' }}"
                    data-parent-active="{{ ($menu->parent && !$menu->parent->isEffectivelyActive()) ? '0' : '1' }}">
                    <i class="fas fa-pencil text-xs"></i>
                </button>

                <button type="button" class="btn-danger w-8 p-1.5!" onclick="deleteMenu({{ $menu->id }})">
                    <i class="fas fa-trash-can text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    @if($menu->children->count())
        <ul id="children-{{ $menu->id }}" class="menu-sortable-list tree-list hidden">
            @foreach($menu->children as $child)
                @include('admin.menus.partials.menu-item', ['menu' => $child])
            @endforeach
        </ul>
    @endif
</li>