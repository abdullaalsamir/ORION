<option value="">⁝⁝⁝ Select Redirect Link ⁝⁝⁝</option>
@foreach($menus as $m)
    @php $mIsCat = $m->children->count() > 0; @endphp
    <option value="/{{ $m->full_slug }}" {{ $mIsCat ? 'disabled' : '' }}
        class="{{ $mIsCat ? 'text-red-800 font-bold' : '' }}">{{ $m->name }}</option>
    @foreach($m->children as $c)
        @php $cIsCat = $c->children->count() > 0; @endphp
        <option value="/{{ $c->full_slug }}" {{ $cIsCat ? 'disabled' : '' }}
            class="{{ $cIsCat ? 'text-red-800 font-bold' : 'text-slate-500' }}"> — {{ $c->name }}</option>
        @foreach($c->children as $sc)
            <option value="/{{ $sc->full_slug }}" class="text-slate-400"> &nbsp;&nbsp;&nbsp; — {{ $sc->name }}</option>
        @endforeach
    @endforeach
@endforeach