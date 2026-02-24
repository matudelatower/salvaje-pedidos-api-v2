<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product'])->latest();
        
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->delivery_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        $orders = $query->paginate(15);
        
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        // Para crear pedidos manualmente si se necesita
        $products = Product::where('active', true)->where('no_stock', false)->get();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'delivery_type' => 'required|in:delivery,pickup',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Calcular totales
        $subtotal = 0;
        $items = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            if ($product->no_stock) {
                return back()->with('error', "El producto {$product->name} no tiene stock disponible.");
            }

            $itemSubtotal = $product->final_price * $item['quantity'];
            $subtotal += $itemSubtotal;

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->final_price,
                'quantity' => $item['quantity'],
                'subtotal' => $itemSubtotal,
            ];
        }

        // Calcular total (aquí se podría agregar costo de delivery)
        $deliveryCost = $request->delivery_type === 'delivery' ? 0 : 0; // Configurar costo de delivery
        $total = $subtotal + $deliveryCost;

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'delivery_type' => $request->delivery_type,
            'subtotal' => $subtotal,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Crear items del pedido
        foreach ($items as $item) {
            $order->orderItems()->create($item);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pedido creado exitosamente.');
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['orderItems.product']);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'delivery_type' => 'required|in:delivery,pickup',
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $order->update($request->all());

        // Enviar notificaciones automáticas de WhatsApp según el cambio de estado
        $this->sendWhatsAppNotification($order, $oldStatus);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pedido actualizado exitosamente.');
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'delivered') {
            return back()->with('error', 'No se puede eliminar un pedido entregado.');
        }

        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Pedido eliminado exitosamente.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Enviar notificación automática
        $this->sendWhatsAppNotification($order, $oldStatus);

        return response()->json(['success' => true, 'status' => $order->status]);
    }

    public function sendWhatsApp(Order $order)
    {
        $message = $this->generateWhatsAppMessage($order);
        $phone = $this->formatPhoneNumber($order->phone);
        
        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
        
        return redirect()->away($whatsappUrl);
    }

    public function sendPaymentPending(Order $order)
    {
        if ($order->payment_status !== 'pending') {
            return back()->with('error', 'Este pedido no tiene pago pendiente.');
        }

        $success = $this->whatsappService->sendPaymentPending($order);

        if ($success) {
            return back()->with('success', 'Mensaje de pago pendiente enviado exitosamente.');
        } else {
            return back()->with('error', 'No se pudo enviar el mensaje. Verifica la configuración de WhatsApp.');
        }
    }

    private function sendWhatsAppNotification(Order $order, string $oldStatus)
    {
        $newStatus = $order->status;

        // Solo enviar notificaciones para cambios de estado específicos
        if ($oldStatus !== $newStatus) {
            switch ($newStatus) {
                case 'confirmed':
                    $this->whatsappService->sendOrderConfirmation($order);
                    break;
                case 'ready':
                    $this->whatsappService->sendOrderReady($order);
                    break;
                case 'delivered':
                    $this->whatsappService->sendOrderDelivered($order);
                    break;
            }
        }
    }

    private function generateWhatsAppMessage(Order $order)
    {
        $items = $order->orderItems->map(function($item) {
            return "• {$item->quantity}x {$item->product_name} - {$item->formatted_price}";
        })->implode("\n");

        return "¡Hola {$order->customer_name}! 👋\n\n" .
               "📋 *PEDIDO #{$order->id}*\n\n" .
               "📦 *Productos:*\n{$items}\n\n" .
               "💰 *Total: {$order->formatted_total}*\n" .
               "📍 *Tipo de envío: " . ($order->delivery_type === 'delivery' ? 'Delivery' : 'Retiro en local') . "*\n\n" .
               "✅ *¡Recibimos tu pedido y lo estamos preparando!*\n\n" .
               "🍔 *Salvaje Bar*";
    }

    private function formatPhoneNumber($phone)
    {
        // Eliminar caracteres no numéricos y agregar código de país si es necesario
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Si no empieza con código de país, agregar 54 (Argentina)
        if (strlen($phone) === 10 && !str_starts_with($phone, '54')) {
            $phone = '54' . $phone;
        }
        
        return $phone;
    }
}
