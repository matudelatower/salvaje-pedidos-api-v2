# 🍔 Salvaje Bar - Sistema de Pedidos

Backoffice completo para gestión de pedidos de Salvaje Bar con integración de MercadoPago y WhatsApp.

## 📋 Características

### 🎯 Módulos Principales
- **✅ Autenticación** - Login con AdminLTE
- **👥 ABM de Usuarios** - Gestión de administradores
- **📂 ABM de Categorías** - Con imágenes y estado activo/inactivo  
- **🍕 ABM de Productos** - Múltiples imágenes/videos, descuentos por período, control de stock
- **🎨 ABM de Banners** - Tipos Principal/Publicitario con ordenamiento
- **⚖️ ABM de Unidades de Medida** - kg, l, ml, un, porc, etc.
- **📦 Gestión de Pedidos** - Sistema completo con estados y filtros
- **💳 MercadoPago API** - Pagos online con preferencias
- **📱 WhatsApp API** - Notificaciones automáticas
- **🔌 API REST** - Endpoints para app móvil

### 🚀 Funcionalidades Especiales

#### Productos
- Múltiples imágenes y videos por producto
- Descuentos por período (inicio/fin)
- Control de stock (sin stock)
- Precio final con descuentos aplicados
- Relación con categorías y unidades

#### Pedidos  
- Estados: Pendiente → Confirmado → Preparando → Listo → Entregado
- Tipos: Delivery / Retiro en local
- Integración con MercadoPago para pagos
- Notificaciones automáticas por WhatsApp
- Filtros avanzados (fecha, estado, cliente)
- Cambio rápido de estado (AJAX)

#### MercadoPago
- Creación de preferencias de pago
- Webhook para notificaciones
- Estados: pending/paid/failed
- URLs de retorno personalizadas

#### WhatsApp
- Mensajes automáticos por cambio de estado
- Plantillas personalizadas
- Formateo de números argentinos
- Botón para enviar mensaje de pago pendiente

## 🛠️ Instalación

### Requisitos
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM

### 1. Clonar e instalar
```bash
git clone <repositorio>
cd salvaje-bar
composer install
npm install
npm run build
```

### 2. Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar base de datos
```bash
# Editar .env con tus credenciales MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salvaje_bar
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

### 4. Ejecutar migraciones y seeders
```bash
php artisan migrate
php artisan db:seed
```

### 5. Configurar servicios

#### MercadoPago
```bash
MERCADOPAGO_ACCESS_TOKEN=tu_access_token
MERCADOPAGO_PUBLIC_KEY=tu_public_key
MERCADOPAGO_SANDBOX=true  # false para producción
```

#### WhatsApp API
```bash
WHATSAPP_API_URL=https://graph.facebook.com/v18.0/
WHATSAPP_TOKEN=tu_whatsapp_token
WHATSAPP_PHONE_NUMBER_ID=tu_phone_number_id
```

### 6. Iniciar servidor
```bash
php artisan serve
```

## 📱 API Endpoints

### Autenticación
- `POST /api/v1/orders` - Crear pedido
- `GET /api/v1/orders/{id}` - Ver pedido
- `GET /api/v1/orders/{id}/status` - Estado del pedido

### Catálogo
- `GET /api/v1/products` - Listar productos activos
- `GET /api/v1/categories` - Listar categorías
- `GET /api/v1/banners` - Listar banners activos

### Pagos
- `POST /api/v1/orders/{id}/payment` - Crear preferencia MercadoPago

## 🎨 Panel de Administración

Acceso al backoffice:
- **URL**: `http://localhost:8000`
- **Usuario**: `admin@salvajebar.com`
- **Contraseña**: `password`

### Módulos disponibles:
1. **Dashboard** - Vista general
2. **Usuarios** - Gestión de administradores
3. **Categorías** - ABM con imágenes
4. **Productos** - ABM completo con multimedia
5. **Banners** - Gestión con ordenamiento
6. **Unidades** - ABM de medidas
7. **Pedidos** - Gestión completa con filtros

## 💡 Flujo de Pedidos

1. **Cliente** crea pedido desde la app móvil
2. **Sistema** genera preferencia de MercadoPago
3. **Cliente** paga via MercadoPago
4. **Webhook** actualiza estado del pago
5. **WhatsApp** envía confirmación automática
6. **Admin** gestiona pedido desde backoffice
7. **Notificaciones** automáticas por cambio de estado

## 🔧 Configuración Adicional

### Storage
```bash
php artisan storage:link
```

### Cola de procesos (opcional)
```bash
php artisan queue:work
```

### Logs
- Logs de MercadoPago: `storage/logs/laravel.log`
- Logs de WhatsApp: `storage/logs/laravel.log`

## 📚 Estructura de Base de Datos

### Tablas principales
- `users` - Administradores del sistema
- `categories` - Categorías de productos
- `products` - Productos con precios y descuentos
- `product_media` - Imágenes y videos de productos
- `units` - Unidades de medida
- `banners` - Banners publicitarios
- `orders` - Pedidos de clientes
- `order_items` - Items de cada pedido

## 🚀 Despliegue

### Producción
1. Configurar variables de entorno
2. Desactivar modo debug: `APP_DEBUG=false`
3. Optimizar: `php artisan optimize`
4. Cachear configuración: `php artisan config:cache`

### Webhooks Configurar
- **MercadoPago**: `https://tudominio.com/mercadopago/webhook`
- **WhatsApp**: Configurar en Meta Developers

## 🤝 Contribuir

1. Fork del proyecto
2. Crear feature branch: `git checkout -b feature/nueva-funcionalidad`
3. Commit: `git commit -m 'Agregar nueva funcionalidad'`
4. Push: `git push origin feature/nueva-funcionalidad`
5. Pull Request

## 📄 Licencia

MIT License - Ver archivo LICENSE

## 🆘 Soporte

Para soporte técnico, contactar a:
- 📧 Email: soporte@salvajebar.com
- 📱 WhatsApp: +54 9 11 XXXX XXXX

---

**Desarrollado con ❤️ para Salvaje Bar**
