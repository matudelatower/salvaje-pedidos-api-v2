@extends('layouts.admin')

@section('title', 'Gestión de Pedidos')
@section('breadcrumb', 'Pedidos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestión de Pedidos</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Pedido
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
                    
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('orders.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="status" class="form-label">Estado</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparando</option>
                                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listo</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="payment_status" class="form-label">Estado de Pago</label>
                                <select name="payment_status" id="payment_status" class="form-control">
                                    <option value="">Todos los pagos</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Pagado</option>
                                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Fallido</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="delivery_type" class="form-label">Tipo de Entrega</label>
                                <select name="delivery_type" id="delivery_type" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="delivery" {{ request('delivery_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                    <option value="pickup" {{ request('delivery_type') == 'pickup' ? 'selected' : '' }}>Retiro</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Fecha Desde</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Fecha Hasta</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre, teléfono...">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->phone }}</td>
                                    <td class="text-right">{{ $order->formatted_total }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : ($order->status == 'ready' ? 'info' : ($order->status == 'preparing' ? 'warning' : ($order->status == 'confirmed' ? 'primary' : 'secondary')))) }}">
                                                {{ $order->status == 'pending' ? 'Pendiente' : ($order->status == 'confirmed' ? 'Confirmado' : ($order->status == 'preparing' ? 'Preparando' : ($order->status == 'ready' ? 'Listo' : ($order->status == 'delivered' ? 'Entregado' : 'Cancelado')))) }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-secondary ml-2 btn-change-status" title="Cambiar estado" data-order-id="{{ $order->id }}" data-current-status="{{ $order->status }}">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'warning') }}">
                                            {{ $order->payment_status == 'paid' ? 'Pagado' : ($order->payment_status == 'failed' ? 'Fallido' : 'Pendiente') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $order->delivery_type == 'delivery' ? 'info' : 'secondary' }}">
                                            {{ $order->delivery_type == 'delivery' ? 'Delivery' : 'Retiro' }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info btn-order-items" title="Ver items del pedido" data-order-id="{{ $order->id }}" onclick="showOrderItems({{ $order->id }}, event)">
                                                <i class="fas fa-list"></i>
                                            </button>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('orders.whatsapp', $order) }}" class="btn btn-sm btn-success" target="_blank">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            @if($order->status !== 'delivered')
                                                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay pedidos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver items del pedido -->
<div class="modal fade" id="itemsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Items del Pedido</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="itemsContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado del pedido -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Estado del Pedido</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="orderId" name="order_id">
                    <div class="form-group">
                        <label for="newStatus">Nuevo Estado:</label>
                        <select name="status" id="newStatus" class="form-control" required>
                            <option value="">Seleccione un estado</option>
                            <option value="pending">Pendiente</option>
                            <option value="confirmed">Confirmado</option>
                            <option value="preparing">Preparando</option>
                            <option value="ready">Listo</option>
                            <option value="delivered">Entregado</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para definir la función globalmente antes que nada -->
<script>
// Variable global para almacenar los datos de los items
window.orderItemsData = {};

// Cargar todos los datos de items inmediatamente (no esperar a document.ready)
@foreach($orders as $order)
    window.orderItemsData[{{ $order->id }}] = {!! json_encode([
        'items' => $order->orderItems->map(function($item) {
            return [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'formatted_price' => '$' . number_format($item->product_price, 2, ',', '.'),
                'formatted_subtotal' => '$' . number_format($item->subtotal, 2, ',', '.')
            ];
        }),
        'total' => $order->formatted_total
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
    console.log('Datos cargados para pedido {{ $order->id }}:', window.orderItemsData[{{ $order->id }}]);
@endforeach

console.log('Todos los datos cargados:', window.orderItemsData);

// Función global para mostrar items en tooltip
window.showOrderItems = function(orderId, event) {
    console.log('showOrderItems llamado con orderId:', orderId);
    
    if (window.orderItemsData && window.orderItemsData[orderId]) {
        var items = window.orderItemsData[orderId].items;
        console.log('Items a mostrar:', items);
        
        var tooltipContent = '<div style="max-width: 350px;">';
        tooltipContent += '<h6 class="mb-2"><i class="fas fa-receipt"></i> Pedido #' + orderId + '</h6>';
        tooltipContent += '<div class="table-responsive">';
        tooltipContent += '<table class="table table-sm table-bordered mb-2">';
        tooltipContent += '<thead class="thead-light"><tr><th>Producto</th><th>Cant.</th><th>Subtotal</th></tr></thead>';
        tooltipContent += '<tbody>';
        
        items.forEach(function(item) {
            tooltipContent += '<tr>';
            tooltipContent += '<td>' + item.product_name + '</td>';
            tooltipContent += '<td class="text-center">' + item.quantity + '</td>';
            tooltipContent += '<td class="text-right">' + item.formatted_subtotal + '</td>';
            tooltipContent += '</tr>';
        });
        
        tooltipContent += '</tbody></table></div>';
        tooltipContent += '<div class="text-right"><strong>Total: ' + window.orderItemsData[orderId].total + '</strong></div>';
        tooltipContent += '</div>';
        
        // Buscar solo el botón específico que fue clickeado usando la clase específica
        var button = $(event.target).closest('.btn-order-items');
        console.log('Botón encontrado:', button.length > 0 ? 'Sí' : 'No');
        
        // Destruir cualquier tooltip o popover existente en este botón específico
        button.tooltip('dispose').popover('dispose');
        
        button.popover({
            html: true,
            content: tooltipContent,
            placement: 'top',
            trigger: 'manual',
            container: 'body',
            template: '<div class="popover popover-items" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>'
        }).popover('show');
        
        console.log('Popover mostrado');
        
        // Cerrar popover después de 6 segundos o al hacer clic fuera
        var timeoutId = setTimeout(function() {
            button.popover('hide');
        }, 6000);
        
        $(document).one('click', function(e) {
            if (!$(e.target).closest('.popover, .btn-order-items').length) {
                clearTimeout(timeoutId);
                button.popover('hide');
            }
        });
    } else {
        console.log('Datos del pedido ' + orderId + ' no disponibles todavía');
        console.log('Keys disponibles:', Object.keys(window.orderItemsData || {}));
    }
};
</script>
@endsection

@push('scripts')
<style>
.popover-items {
    max-width: 350px !important;
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.popover-items .popover-body {
    padding: 0.75rem;
}

.popover-items .table {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.popover-items .table th {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    font-size: 0.8rem;
}

.popover-items .table td {
    vertical-align: middle;
}

.popover-items h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.popover-items .text-right {
    border-top: 1px solid #dee2e6;
    padding-top: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}
</style>
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Abrir modal para cambiar estado
    $('.btn-change-status').click(function() {
        var orderId = $(this).data('order-id');
        var currentStatus = $(this).data('current-status');
        
        $('#orderId').val(orderId);
        $('#newStatus').val(currentStatus);
        $('#statusModal').modal('show');
    });
    
    // Enviar formulario para cambiar estado
    $('#statusForm').submit(function(e) {
        e.preventDefault();
        
        var orderId = $('#orderId').val();
        var status = $('#newStatus').val();
        
        $.post(`/orders/${orderId}/status`, {
            _token: '{{ csrf_token() }}',
            status: status
        }).done(function(response) {
            // Cerrar modal
            $('#statusModal').modal('hide');
            
            // Mostrar notificación de éxito
            var alert = $('<div class="alert alert-success alert-dismissible fade show">')
                .html('Estado actualizado exitosamente')
                .append('<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');
            
            $('.card-body').prepend(alert);
            
            // Recargar la página para mostrar los cambios
            setTimeout(function() {
                location.reload();
            }, 1500);
        }).fail(function() {
            alert('Error al actualizar el estado');
        });
    });
});
</script>
@endpush
