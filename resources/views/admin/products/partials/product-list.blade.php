<div class="admin-card-header p-5!">
    <div class="w-3/4">
        <h1 class="whitespace-normal leading-tight pr-4">
            {{ $generic ? $generic->name : 'Archived Products' }}
        </h1>
    </div>
    @if($generic)
        <button onclick="openAddProduct()" class="btn-success h-10 shrink-0">
            <i class="fas fa-plus"></i> Add Product
        </button>
    @endif
</div>

<div class="admin-card-body flex flex-col custom-scrollbar bg-slate-50/20">
    <div id="product-list-container" class="space-y-3 flex-1">
        @forelse($products as $p)
            @php $isLocked = $generic ? !$generic->is_active : false; @endphp
            <div
                class="group bg-white border border-slate-200 rounded-2xl p-3 flex items-center hover:border-admin-blue transition-all {{ ($isLocked || !$p->is_active) ? 'opacity-70' : '' }}">

                <div class="w-40 aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                    <img src="{{ asset('storage/' . $p->image_path) }}"
                        class="w-full h-full object-cover transition-all duration-500 {{ ($isLocked || !$p->is_active) ? 'grayscale' : '' }}">
                </div>

                <div class="flex-1 min-w-0 flex flex-col gap-0.5 ml-5">
                    <span class="font-semibold text-slate-700 text-sm truncate tracking-tight">{{ $p->trade_name }}</span>
                    <span
                        class="text-admin-blue font-bold text-[11px] truncate tracking-wider">{{ $p->therapeutic_class ?? 'No Class Defined' }}</span>
                    <p class="text-[11px] text-slate-400 mt-1 truncate">{{ $p->preparation }}</p>
                </div>

                <div class="flex items-center gap-4 shrink-0 px-6">
                    @if($isLocked)
                        <span class="badge badge-danger tracking-normal!">Locked by Generic</span>
                    @else
                        <span class="badge {{ $p->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $p->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    @endif
                </div>

                <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                    @if(!$isLocked)
                        <button class="btn-icon w-8 p-1.5!" onclick="openEditProduct({{ json_encode($p) }})">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>
                    @endif
                    <button class="btn-danger w-8 p-1.5!" onclick="deleteProduct({{ $p->id }})">
                        <i class="fas fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div
                class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                <i class="fas fa-capsules text-4xl mb-4"></i>
                <h2 class="text-slate-400!">No Products Found in this Category</h2>
            </div>
        @endforelse
    </div>
</div>