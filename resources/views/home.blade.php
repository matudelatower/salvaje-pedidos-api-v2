@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ App\Models\Order::count() }}</h3>
                <p>Total Pedidos</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('orders.index') }}" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ App\Models\Product::where('active', true)->count() }}</h3>
                <p>Productos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-hamburger"></i>
            </div>
            <a href="{{ route('products.index') }}" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ App\Models\Category::where('active', true)->count() }}</h3>
                <p>Categorías</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
            <a href="{{ route('categories.index') }}" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ App\Models\User::count() }}</h3>
                <p>Usuarios</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('users.index') }}" class="small-box-footer">
                Más info <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="row">
    <div class="col-md-8">
        <!-- PEDIDOS RECIENTES -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shopping-cart mr-1"></i>
                    Pedidos Recientes
                </h3>
                <div class="card-tools">
                    <a href="{{ route('orders.index') }}" class="btn btn-tool btn-sm">
                        <i class="fas fa-list"></i> Ver todos
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $recentOrders = App\Models\Order::with('orderItems')->latest()->limit(5)->get();
                @endphp
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->formatted_total }}</td>
                                    <td>
                                        <span class="badge badge-{{ $order->status_color }}">
                                            {{ __('orders.status.' . $order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @else
                    <div class="p-3 text-center">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay pedidos recientes</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- ESTADÍSTICAS -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Estadísticas
                </h3>
            </div>
            <div class="card-body">
                @php
                    $todayOrders = App\Models\Order::whereDate('created_at', today())->count();
                    $pendingOrders = App\Models\Order::where('status', 'pending')->count();
                    $paidOrders = App\Models\Order::where('payment_status', 'paid')->count();
                @endphp
                
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pedidos Hoy</span>
                        <span class="info-box-number">{{ $todayOrders }}</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pendientes</span>
                        <span class="info-box-number">{{ $pendingOrders }}</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pagados</span>
                        <span class="info-box-number">{{ $paidOrders }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PRODUCTOS SIN STOCK -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Productos sin Stock
                </h3>
            </div>
            <div class="card-body p-0">
                @php
                    $outOfStock = App\Models\Product::where('no_stock', true)->limit(5)->get();
                @endphp
                @if($outOfStock->count() > 0)
                    <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($outOfStock as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>
                                        <span class="badge badge-danger">Sin Stock</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @else
                    <div class="p-3 text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted">Todos los productos con stock</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
