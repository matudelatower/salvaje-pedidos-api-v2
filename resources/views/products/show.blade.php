@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Producto</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Información General</h4>
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Categoría:</th>
                                    <td>{{ $product->category->name }}</td>
                                </tr>
                                @if($product->unit)
                                <tr>
                                    <th>Unidad:</th>
                                    <td>{{ $product->unit->name }} ({{ $product->unit->abbreviation }})</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Precio:</th>
                                    <td>${{ number_format($product->price, 2, ',', '.') }}</td>
                                </tr>
                                @if($product->hasDiscount())
                                <tr>
                                    <th>Precio con Descuento:</th>
                                    <td class="text-success">${{ number_format($product->final_price, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Descuento:</th>
                                    <td>{{ $product->discount_percentage }}% ({{ $product->discount_start->format('d/m/Y H:i') }} - {{ $product->discount_end->format('d/m/Y H:i') }})</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Stock:</th>
                                    <td>
                                        @if($product->no_stock)
                                            <span class="badge badge-danger">Sin Stock</span>
                                        @else
                                            <span class="badge badge-success">Disponible</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span class="badge badge-{{ $product->active ? 'success' : 'danger' }}">
                                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Descripción</h4>
                            @if($product->description)
                                <p>{{ $product->description }}</p>
                            @else
                                <p class="text-muted">Sin descripción</p>
                            @endif
                            
                            <h4>Ingredientes</h4>
                            @if($product->ingredients)
                                <p>{{ $product->ingredients }}</p>
                            @else
                                <p class="text-muted">Sin ingredientes especificados</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Imágenes y Videos</h4>
                            @if($product->media->count() > 0)
                                <div class="row">
                                    @foreach($product->media as $media)
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                @if($media->file_type == 'image')
                                                    <img src="{{ Storage::url($media->file_path) }}" alt="{{ $product->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                @else
                                                    <video controls class="card-img-top" style="height: 200px; object-fit: cover;">
                                                        <source src="{{ Storage::url($media->file_path) }}" type="video/mp4">
                                                        Tu navegador no soporta videos.
                                                    </video>
                                                @endif
                                                <div class="card-body p-2">
                                                    <small class="text-muted">{{ $media->file_type == 'image' ? 'Imagen' : 'Video' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No hay imágenes o videos</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
