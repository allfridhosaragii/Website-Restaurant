@extends('layouts.admin')
@section('title', __('messages.manage_menus'))
@section('content')
<section class="section bg-cream">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">{{ __('messages.manage_menus') }}</h3>
                        <p class="text-muted mb-0">{{ __('messages.manage_menus_desc') }}</p>
                    </div>
                    <a href="/admin/menus/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>{{ __('messages.add_menu') }}
                    </a>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.menu_image') }}</th>
                                <th>{{ __('messages.menu_name') }}</th>
                                <th>{{ __('messages.menu_category') }}</th>
                                <th>{{ __('messages.menu_price') }}</th>
                                <th>{{ __('messages.menu_status') }}</th>
                                <th class="text-end">{{ __('messages.menu_action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                                <tr>
                                    <td>
                                        @if($menu->image_url)
                                            <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" 
                                                 class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $menu->name }}</strong>
                                        @if($menu->description)
                                            <br><small class="text-muted">{{ Str::limit($menu->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $menu->category }}</span></td>
                                    <td><strong>Rp {{ number_format($menu->price, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @if($menu->is_available)
                                            <span class="badge bg-success">{{ __('messages.status_available') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('messages.status_out_of_stock') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/menus/{{ $menu->slug }}/edit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/admin/menus/{{ $menu->slug }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('{{ __('messages.delete_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mb-0">{{ __('messages.no_menu_list') }}</p>
                                        <a href="/admin/menus/create" class="btn btn-primary mt-3">{{ __('messages.add_first_menu') }}</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>{{ __('messages.back_dashboard') }}
            </a>
        </div>
    </div>
</section>
@endsection