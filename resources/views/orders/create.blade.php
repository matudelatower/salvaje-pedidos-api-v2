@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nuevo Pedido</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
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
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
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
                                           id="address" name="address" value="{{ old('address') }}">
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
                                        <option value="">Seleccionar tipo</option>
                                        <option value="delivery" {{ old('delivery_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                                        <option value="pickup" {{ old('delivery_type') == 'pickup' ? 'selected' : '' }}>Retiro en local</option>
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
                            <div class="col-12">
                                <h4>Items del Pedido</h4>
                                <div id="itemsContainer">
                                    <div class="item-row">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <select name="items[0][product_id]" class="form-control product-select" required>
                                                    <option value="">Seleccionar producto</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->final_price }}" data-name="{{ $product->name }}">
                                                            {{ $product->name }} - ${{ number_format($product->final_price, 2, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control subtotal-input" readonly value="$0.00">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-info btn-sm mt-2" id="addItem">
                                    <i class="fas fa-plus"></i> Agregar Item
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes">Notas</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Resumen del Pedido</h5>
                                        <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td class="text-right" id="subtotal">$0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Delivery:</td>
                                                <td class="text-right" id="deliveryCost">$0.00</td>
                                            </tr>
                                            <tr>
                                                <th>Total:</th>
                                                <th class="text-right" id="total">$0.00</th>
                                            </tr>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Pedido
                            </button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                        
                        <!-- Campos ocultos para los items -->
                        <div id="hiddenFields"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let itemCount = 1;
    
    function calculateTotals() {
        let subtotal = 0;
        
        $('.item-row').each(function() {
            const productSelect = $(this).find('.product-select');
            const quantityInput = $(this).find('.quantity-input');
            const subtotalInput = $(this).find('.subtotal-input');
            
            const price = parseFloat(productSelect.find(':selected').data('price')) || 0;
            const quantity = parseInt(quantityInput.val()) || 0;
            const itemSubtotal = price * quantity;
            
            subtotalInput.val('$' + itemSubtotal.toFixed(2).replace('.', ','));
            subtotal += itemSubtotal;
        });
        
        const deliveryType = $('#delivery_type').val();
        const deliveryCost = deliveryType === 'delivery' ? 0 : 0; // Configurar costo de delivery
        const total = subtotal + deliveryCost;
        
        $('#subtotal').text('$' + subtotal.toFixed(2).replace('.', ','));
        $('#deliveryCost').text('$' + deliveryCost.toFixed(2).replace('.', ','));
        $('#total').text('$' + total.toFixed(2).replace('.', ','));
    }
    
    function addItemRow() {
        const newRow = `
            <div class="item-row">
                <div class="row">
                    <div class="col-md-5">
                        <select name="items[${itemCount}][product_id]" class="form-control product-select" required>
                            <option value="">Seleccionar producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->final_price }}" data-name="{{ $product->name }}">
                                    {{ $product->name }} - ${{ number_format($product->final_price, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[${itemCount}][quantity]" class="form-control quantity-input" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control subtotal-input" readonly value="$0.00">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#itemsContainer').append(newRow);
        itemCount++;
        
        // Mostrar botones de eliminar si hay más de un item
        updateRemoveButtons();
    }
    
    function updateRemoveButtons() {
        const itemRows = $('.item-row');
        $('.remove-item').toggle(itemRows.length > 1);
    }
    
    // Event handlers
    $(document).on('change', '.product-select, .quantity-input', calculateTotals);
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        calculateTotals();
        updateRemoveButtons();
    });
    
    $('#addItem').click(addItemRow);
    $('#delivery_type').change(calculateTotals);
    
    // Calcular totales iniciales
    calculateTotals();
    updateRemoveButtons();
});
</script>
@endsection
