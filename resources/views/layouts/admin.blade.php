<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        .password-controls {
            gap: 0.5rem;
        }
        
        .password-controls .btn {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
        
        .password-strength .progress {
            background-color: #e9ecef;
        }
        
        .password-strength .strength-bar {
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .password-strength .strength-text {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .alert {
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Fix navbar height and text overflow */
        .main-header {
            min-height: 57px;
        }
        
        .main-header .navbar {
            min-height: 57px;
        }
        
        .main-header .nav-link {
            height: auto;
            white-space: nowrap;
            line-height: 1.5;
        }
        
        .user-menu .nav-link {
            display: flex;
            align-items: center;
            height: auto;
            padding: 0.5rem 1rem;
        }
        
        .user-menu .nav-link span {
            line-height: 1.2;
            margin-left: 0.5rem;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed dark-mode">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <i class="fas fa-spinner fa-spin"></i>
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('home') }}" class="nav-link">{{ config('app.name') }}</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Order Status Toggle -->
                <li class="nav-item">
                    <div class="nav-link d-flex align-items-center">
                        <span class="mr-2">Abierto:</span>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="ordersToggle" 
                                   {{ \App\Models\Setting::get('orders_enabled', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="ordersToggle"></label>
                        </div>
                    </div>
                </li>
                
                <!-- Messages Dropdown Menu -->
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-comments"></i>
                        <span class="badge badge-danger navbar-badge">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="#" class="dropdown-item">
                            <!-- Message Start -->
                            <div class="media">
                                <img src="{{ asset('dist/img/user1-128x128.jpg') }}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                                <div class="media-body">
                                    <h3 class="dropdown-item-title">
                                        Brad Diesel
                                        <span class="float-right text-sm text-danger">12h</span>
                                    </h3>
                                    <p class="text-sm">Not sure if this is a good idea...</p>
                                </div>
                            </div>
                            <!-- Message End -->
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                    </div>
                </li> --}}
                
                {{-- <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li> --}}
                
                <!-- Fullscreen -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
                
                <!-- User Account Menu -->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ asset('storage/img/icon180.png') }}" class="user-image img-circle elevation-2" alt="User Image">
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <!-- User image -->
                        <li class="user-header bg-primary">
                            <img src="{{ asset('storage/img/icon180.png') }}" class="img-circle elevation-2" alt="User Image">
                            <p>
                                {{ Auth::user()->name }}
                                <small>{{ Auth::user()->role === 'admin' ? 'Administrador' : 'Usuario' }}</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        {{-- <li class="user-body">
                            <div class="row">
                                <div class="col-4 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-4 text-center">
                                    <a href="#">Sales</a>
                                </div>
                                <div class="col-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </div>
                        </li> --}}
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <a href="{{ route('users.edit', Auth::user()->id) }}" class="btn btn-default btn-flat">Perfil</a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-default btn-flat float-right">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="brand-link">
                {{-- <img src="{{ asset('storage/img/logo_blanco.png') }}" alt="Salvaje Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
                <img src="{{ asset('storage/img/logo_blanco.png') }}" alt="Salvaje Logo" class="brand-image" style="opacity: .8">
                <span class="brand-text font-weight-light">
                    {{ config('app.name') }}
                </span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ asset('storage/img/icon180.png') }}" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block text-white">{{ Auth::user()->name }}</a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">GESTIÓN</li>
                        
                        @if(Auth::user()->canManageOrdersAndBanners())
                        <li class="nav-item">
                            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->is('orders*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>
                                    Pedidos
                                    <span class="badge badge-info right">New</span>
                                </p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('banners.index') }}" class="nav-link {{ request()->is('banners*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-image"></i>
                                <p>Banners</p>
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->canManageFullSystem())
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-hamburger"></i>
                                <p>Productos</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Categorías</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('units.index') }}" class="nav-link {{ request()->is('units*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-balance-scale"></i>
                                <p>Unidades</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">ADMINISTRACIÓN</li>
                        
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('warning') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ config('app.name') }}</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- Custom scripts -->
    @stack('scripts')
    
    <script>
    $(document).ready(function() {
        $('#ordersToggle').change(function() {
            var enabled = $(this).is(':checked');
            
            $.ajax({
                url: '{{ route("settings.toggle-orders") }}',
                method: 'POST',
                data: {
                    enabled: enabled,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Show notification
                    var alertClass = response.enabled ? 'success' : 'warning';
                    var alertHtml = '<div class="alert alert-' + alertClass + ' alert-dismissible">' +
                                   '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                                   response.message +
                                   '</div>';
                    
                    $('.content-wrapper .container-fluid').prepend(alertHtml);
                    
                    // Auto-hide after 3 seconds
                    setTimeout(function() {
                        $('.alert').fadeOut();
                    }, 3000);
                },
                error: function() {
                    // Revert toggle on error
                    $('#ordersToggle').prop('checked', !enabled);
                    alert('Error al actualizar el estado de pedidos');
                }
            });
        });
    });
    </script>
</body>
</html>
