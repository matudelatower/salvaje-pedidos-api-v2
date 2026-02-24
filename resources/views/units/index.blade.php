@extends('layouts.admin')

@section('title', 'Unidades de Medida')
@section('breadcrumb', 'Unidades')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Unidades de Medida</h3>
                    <div class="card-tools">
                        <a href="{{ route('units.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Unidad
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                                <th>Estado</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                                <tr>
                                    <td>{{ $unit->id }}</td>
                                    <td>{{ $unit->name }}</td>
                                    <td><code>{{ $unit->abbreviation }}</code></td>
                                    <td>
                                        <span class="badge badge-{{ $unit->active ? 'success' : 'danger' }}">
                                            {{ $unit->active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $unit->products->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('units.destroy', $unit) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')" {{ $unit->products->count() > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay unidades registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $units->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
