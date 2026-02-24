@extends('layouts.admin')

@section('title', 'Banners')
@section('breadcrumb', 'Banners')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Banners</h3>
                    <div class="card-tools">
                        <a href="{{ route('banners.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Banner
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div id="banners-container">
                        @forelse($banners as $banner)
                            <div class="banner-item" data-id="{{ $banner->id }}" style="cursor: move;">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-1">
                                                <i class="fas fa-grip-vertical text-muted"></i>
                                            </div>
                                            <div class="col-md-2">
                                                @if($banner->image)
                                                    <img src="{{ Storage::url($banner->image) }}" alt="{{ $banner->name }}" class="img-thumbnail" style="max-width: 80px; max-height: 60px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted">Sin imagen</span>
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <strong>{{ $banner->name }}</strong>
                                                @if($banner->url)
                                                    <br><small class="text-muted">{{ $banner->url }}</small>
                                                @endif
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge badge-{{ $banner->type == 'principal' ? 'primary' : 'info' }}">
                                                    {{ $banner->type == 'principal' ? 'Principal' : 'Publicitario' }}
                                                </span>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="badge badge-{{ $banner->active ? 'success' : 'danger' }}">
                                                    {{ $banner->active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </div>
                                            <div class="col-md-2 text-right">
                                                <div class="btn-group">
                                                    <a href="{{ route('banners.edit', $banner) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('banners.destroy', $banner) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                <p class="text-muted">No hay banners registrados</p>
                                <a href="{{ route('banners.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primer Banner
                                </a>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="d-flex justify-content-center mt-3">
                        {{ $banners->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    $("#banners-container").sortable({
        items: '.banner-item',
        handle: '.fa-grip-vertical',
        update: function(event, ui) {
            var banners = [];
            $('.banner-item').each(function(index) {
                banners.push($(this).data('id'));
            });
            
            $.post('{{ route("banners.reorder") }}', {
                _token: '{{ csrf_token() }}',
                banners: banners
            });
        }
    });
});
</script>
@endsection
