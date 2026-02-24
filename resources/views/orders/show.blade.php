@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Pedido #{{ $order->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('orders.whatsapp', $order) }}" class="btn btn-success btn-sm" target="_blank">
                            <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Información del Cliente</h4>
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono:</th>
                                    <td>{{ $order->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Dirección:</th>
                                    <td>{{ $order->address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo de Entrega:</th>
                                    <td>
                                        <span class="badge badge-{{ $order->delivery_type == 'delivery' ? 'info' : 'secondary' }}">
                                            {{ $order->delivery_type == 'delivery' ? 'Delivery' : 'Retiro en local' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Estado del Pedido</h4>
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span class="badge badge-{{ $order->status_color }}">
                                            {{ __('orders.status.' . $order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado de Pago:</th>
                                    <td>
                                        <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'warning') }}">
                                            {{ $order->payment_status == 'paid' ? 'Pagado' : ($order->payment_status == 'failed' ? 'Fallido' : 'Pendiente') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha del Pedido:</th>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Última Actualización:</th>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Items del Pedido</h4>
                            <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->formatted_price }}</td>
                                            <td>{{ $item->formatted_subtotal }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Subtotal:</th>
                                        <td>{{ $order->formatted_subtotal }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3">Total:</th>
                                        <td><strong>{{ $order->formatted_total }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                        </div>
                    </div>
                    
                    @if($order->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Notas</h4>
                            <div class="alert alert-info">
                                {{ $order->notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Pedido
                            </a>
                            @if($order->status !== 'delivered')
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este pedido?')">
                                        <i class="fas fa-trash"></i> Eliminar Pedido
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
