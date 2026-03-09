<div class="admin-card-header">
    <div class="flex flex-col">
        <h1>{{ $menu->name }}</h1>
        <p class="text-xs text-slate-400">Manage banners for this page</p>
    </div>

    <button onclick="openBannerUploadModal()" class="btn-success h-10!">
        <i class="fas fa-plus"></i> Add Image
    </button>
</div>

<div class="admin-card-body custom-scrollbar bg-slate-50/20">
    <div class="grid grid-cols-1 gap-4">
        @forelse($banners as $banner)
            @php
                $actualWidth = $banner->image_width ?? 'N/A';
                $actualHeight = $banner->image_height ?? 'N/A';
                $ratioStr = 'N/A';

                if (is_numeric($actualWidth) && is_numeric($actualHeight) && $actualHeight > 0) {
                    $calcRatio = $actualWidth / $actualHeight;

                    if (abs($calcRatio - (48 / 9)) < 0.1) {
                        $ratioStr = '48:9';
                    } elseif (abs($calcRatio - (23 / 9)) < 0.1) {
                        $ratioStr = '23:9';
                    } elseif (abs($calcRatio - (16 / 9)) < 0.1) {
                        $ratioStr = '16:9';
                    } else {
                        $ratioStr = round($calcRatio, 2) . ':1';
                    }
                }
            @endphp

            <div
                class="relative group rounded-2xl border border-slate-200 bg-white p-1 hover:border-admin-blue transition-all">
                <div class="rounded-xl overflow-hidden bg-slate-100 relative shimmer">
                    <img src="{{ url($menu->full_slug . '/' . $banner->file_name) }}?v={{ time() }}"
                        width="{{ is_numeric($actualWidth) ? $actualWidth : '' }}"
                        height="{{ is_numeric($actualHeight) ? $actualHeight : '' }}"
                        class="w-full h-full object-cover transition-all duration-500 {{ !$banner->is_active ? 'opacity-40 grayscale' : '' }}"
                        alt="banner" onload="this.parentElement.classList.remove('shimmer')">

                    @if(!$banner->is_active)
                        <div class="absolute inset-0 flex items-center justify-center z-10">
                            <span class="badge badge-danger bg-red-600! text-white! border-none!">Inactive</span>
                        </div>
                    @endif

                    <div class="absolute bottom-3 left-3 flex flex-col items-start gap-1 z-20">
                        <span
                            class="bg-slate-900/50 backdrop-blur text-white text-[11px] px-2 py-1 rounded-md tracking-wider">
                            Ratio: {{ $ratioStr }}
                        </span>
                        <span
                            class="bg-slate-900/50 backdrop-blur text-white text-[11px] px-2 py-1 rounded-md tracking-wider">
                            Max Width: {{ $actualWidth }}{{ $actualWidth !== 'N/A' ? 'px' : '' }}
                        </span>
                    </div>
                </div>

                <div
                    class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 z-30">
                    <button
                        class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur flex items-center justify-center text-xs text-slate-600 hover:text-white hover:bg-admin-blue transition-all cursor-pointer"
                        onclick="openBannerEditModal({{ $banner->id }}, '{{ $banner->file_name }}', '{{ $menu->full_slug }}', {{ $banner->is_active }})">
                        <i class="fas fa-pencil"></i>
                    </button>
                    <button
                        class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur flex items-center justify-center text-xs text-red-500 hover:bg-red-500 hover:text-white transition-all cursor-pointer"
                        onclick="deleteBannerImage({{ $banner->id }})">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
        @empty
            <div
                class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                <i class="fas fa-cloud-arrow-up text-4xl mb-4"></i>
                <h2 class="text-slate-400!">No Banners Uploaded Yet</h2>
            </div>
        @endforelse
    </div>
</div>