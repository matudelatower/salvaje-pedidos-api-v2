@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Producto</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required>
                                        <option value="">Seleccionar categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Precio <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit_id">Unidad de Medida</label>
                                    <select class="form-control @error('unit_id') is-invalid @enderror" 
                                            id="unit_id" name="unit_id">
                                        <option value="">Seleccionar unidad</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }} ({{ $unit->abbreviation }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ingredients">Ingredientes</label>
                                    <textarea class="form-control @error('ingredients') is-invalid @enderror" 
                                              id="ingredients" name="ingredients" rows="3">{{ old('ingredients', $product->ingredients) }}</textarea>
                                    @error('ingredients')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_percentage">Descuento (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage', $product->discount_percentage) }}">
                                    @error('discount_percentage')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_start">Inicio Descuento</label>
                                    <input type="datetime-local" class="form-control @error('discount_start') is-invalid @enderror" 
                                           id="discount_start" name="discount_start" value="{{ old('discount_start', $product->discount_start ? $product->discount_start->format('Y-m-d\TH:i') : '') }}">
                                    @error('discount_start')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_end">Fin Descuento</label>
                                    <input type="datetime-local" class="form-control @error('discount_end') is-invalid @enderror" 
                                           id="discount_end" name="discount_end" value="{{ old('discount_end', $product->discount_end ? $product->discount_end->format('Y-m-d\TH:i') : '') }}">
                                    @error('discount_end')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="media_files">Agregar Imágenes y Videos</label>
                                    <input type="file" class="form-control @error('media_files.*') is-invalid @enderror" 
                                           id="media_files" name="media_files[]" multiple accept="image/*,video/*">
                                    @error('media_files.*')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Puedes subir múltiples imágenes y videos. Máximo 10MB por archivo.</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($product->media->count() > 0)
                        <div class="row">
                            <div class="col-12">
                                <h4>Archivos Actuales</h4>
                                <div class="row">
                                    @foreach($product->media as $media)
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                @if($media->file_type == 'image')
                                                    <img src="{{ Storage::url($media->file_path) }}" alt="{{ $product->name }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                @else
                                                    <video controls class="card-img-top" style="height: 150px; object-fit: cover;">
                                                        <source src="{{ Storage::url($media->file_path) }}" type="video/mp4">
                                                    </video>
                                                @endif
                                                <div class="card-body p-2 text-center">
                                                    <small class="text-muted">{{ $media->file_type == 'image' ? 'Imagen' : 'Video' }}</small><br>
                                                    <button type="button" class="btn btn-sm btn-danger delete-media" data-media-id="{{ $media->id }}">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="no_stock" name="no_stock" value="1" {{ $product->no_stock ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="no_stock">Sin Stock</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ $product->active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="active">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('.delete-media').click(function() {
        var mediaId = $(this).data('media-id');
        if (confirm('¿Está seguro de eliminar este archivo?')) {
            $.ajax({
                url: '{{ route("products.media.delete", ["media" => "PLACEHOLDER"]) }}'.replace('PLACEHOLDER', mediaId),
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Error al eliminar el archivo');
                }
            });
        }
    });
});
</script>
@endsection
