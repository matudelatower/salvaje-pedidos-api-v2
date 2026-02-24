@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Pedido #{{ $order->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
                                    @error('customer_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Teléfono <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $order->phone) }}" required>
                                    @error('phone')
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
                                    <label for="address">Dirección</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $order->address) }}">
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Obligatorio solo para delivery</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_type">Tipo de Entrega <span class="text-danger">*</span></label>
                                    <select class="form-control @error('delivery_type') is-invalid @enderror" 
                                            id="delivery_type" name="delivery_type" required>
                                        <option value="delivery" {{ old('delivery_type', $order->delivery_type) == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                        <option value="pickup" {{ old('delivery_type', $order->delivery_type) == 'pickup' ? 'selected' : '' }}>Retiro en local</option>
                                    </select>
                                    @error('delivery_type')
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
                                    <label for="status">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="confirmed" {{ old('status', $order->status) == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                        <option value="preparing" {{ old('status', $order->status) == 'preparing' ? 'selected' : '' }}>Preparando</option>
                                        <option value="ready" {{ old('status', $order->status) == 'ready' ? 'selected' : '' }}>Listo</option>
                                        <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_status">Estado de Pago <span class="text-danger">*</span></label>
                                    <select class="form-control @error('payment_status') is-invalid @enderror" 
                                            id="payment_status" name="payment_status" required>
                                        <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Pagado</option>
                                        <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>Fallido</option>
                                    </select>
                                    @error('payment_status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Notas</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Items del pedido (solo lectura) -->
                        <div class="row">
                            <div class="col-12">
                                <h4>Items del Pedido</h4>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Los items del pedido no se pueden editar desde aquí para mantener la integridad de los datos.
                                </div>
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
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Pedido
                            </button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
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
