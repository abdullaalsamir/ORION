@extends('admin.layouts.app')

@section('title', $menu->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 style="margin:0 0 4px 0;">{{ $menu->name }}</h3>
            <small style="color:#666">
                This is a multifunctional module. Content and features will be implemented here later.
            </small>
        </div>

        <div class="card-body"
            style="margin-top:18px; min-height: 300px; display: flex; align-items: center; justify-content: center; border: 2px dashed #e6e9ee; border-radius: 8px;">
            <div style="text-align: center; color: #a0aec0;">
                <i class="fas fa-tools" style="font-size: 48px; margin-bottom: 15px;"></i>
                <p>Module <strong>"{{ $menu->name }}"</strong> is under construction.</p>
            </div>
        </div>
    </div>
@endsection