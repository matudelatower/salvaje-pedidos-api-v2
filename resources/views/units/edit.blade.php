@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Unidad de Medida</h3>
                    <div class="card-tools">
                        <a href="{{ route('units.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.update', $unit) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="abbreviation">Abreviatura <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" 
                                           id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required maxlength="10">
                                    @error('abbreviation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Ej: kg, l, ml, un, porc</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ $unit->active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="active">Activa</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($unit->products->count() > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Advertencia:</strong> Esta unidad está siendo utilizada por {{ $unit->products->count() }} producto(s). 
                                    Si la desactiva, los productos asociados no podrán usar esta unidad.
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
