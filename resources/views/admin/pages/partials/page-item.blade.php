@if($menu->slug === 'home') @php return; @endphp @endif

<li data-id="{{ $menu->id }}" class="sortable-item tree-item group">
    <div
        class="flex items-center justify-between p-4.5 bg-white border border-slate-200 rounded-2xl hover:border-admin-blue/50 transition-all relative z-10">
        <div class="flex items-center gap-3">

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
            @if($menu->children->isEmpty())
                @if(!$menu->is_multifunctional)
                    @if(empty($menu->content))
                        <span class="badge badge-danger text-[9px]!">No Content</span>
                    @else
                        <span class="badge badge-success text-[9px]!">Content Added</span>
                    @endif
                @else
                    <span class="badge badge-purple text-[9px]!">Multifunctional</span>
                @endif
            @endif

            <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                @if($menu->children->isEmpty())
                    @if($menu->is_multifunctional)
                        <a href="{{ url('admin/' . $menu->slug) }}" class="btn-icon w-8 p-1.5! text-admin-blue"
                            title="Go to module">
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    @else
                        <button type="button" class="btn-icon edit-page w-8 p-1.5!" data-id="{{ $menu->id }}"
                            data-name="{{ $menu->name }}" data-content="{{ e($menu->content) }}">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                    @endif
                @else
                    <div class="w-8"></div>
                @endif
            </div>
        </div>
    </div>

    @if($menu->children->count())
        <ul id="children-{{ $menu->id }}" class="menu-sortable-list tree-list hidden">
            @foreach($menu->children as $child)
                @include('admin.pages.partials.page-item', ['menu' => $child])
            @endforeach
        </ul>
    @endif
</li>