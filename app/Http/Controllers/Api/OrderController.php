<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\MercadoPagoService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private $mercadopagoService;
    private $whatsappService;

    public function __construct(MercadoPagoService $mercadopagoService, WhatsAppService $whatsappService)
    {
        $this->mercadopagoService = $mercadopagoService;
        $this->whatsappService = $whatsappService;
    }

    /**
     * Crear un nuevo pedido desde la app
     */
    public function store(Request $request)
    {
        // Verificar si los pedidos están habilitados
        if (!Setting::get('orders_enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Los pedidos están deshabilitados temporalmente. Por favor, inténtelo más tarde.',
                'code' => 'ORDERS_DISABLED'
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'delivery_type' => 'required|in:delivery,pickup',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar stock y calcular totales
            $subtotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if (!$product->active) {
                    return response()->json([
                        'success' => false,
                        'message' => "El producto {$product->name} no está disponible."
                    ], 400);
                }

                if ($product->no_stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "El producto {$product->name} no tiene stock disponible."
                    ], 400);
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

            // Cargar relaciones para la respuesta
            $order->load(['orderItems.product']);

            // Enviar notificación de WhatsApp automática
            $this->whatsappService->sendOrderConfirmation($order);

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de un pedido
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product']);
        
        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Obtener items de un pedido
     */
    public function items(Order $order)
    {
        $items = $order->orderItems()->with('product')->get();
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $order->formatted_total
        ]);
    }

    /**
     * Crear preferencia de pago con MercadoPago
     */
    public function createPaymentPreference(Order $order)
    {
        try {
            if ($order->payment_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido ya tiene un pago procesado'
                ], 400);
            }

            // Formatear items para MercadoPago
            $items = $this->mercadopagoService->formatItems($order->orderItems->toArray());

            // Formatear información del pagador
            $payer = $this->mercadopagoService->formatPayer([
                'customer_name' => $order->customer_name,
                'phone' => $order->phone,
                'address' => $order->address,
            ]);

            // Crear preferencia
            $preference = $this->mercadopagoService->createPreference([
                'items' => $items,
                'payer' => $payer,
                'external_reference' => 'order_' . $order->id,
            ]);

            // Guardar ID de preferencia en el pedido
            $order->update([
                'mercadopago_preference_id' => $preference->id,
            ]);

            return response()->json([
                'success' => true,
                'preference_id' => $preference->id,
                'payment_url' => $this->mercadopagoService->getPaymentUrl($preference),
                'items' => $items,
                'total' => $order->formatted_total,
            ]);

        } catch (\Exception $e) {
            Log::error('API MercadoPago preference creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la preferencia de pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar estado de un pedido
     */
    public function status(Order $order)
    {
        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'updated_at' => $order->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Obtener productos disponibles
     */
    public function products()
    {
        $products = Product::where('active', true)
            ->where('no_stock', false)
            ->with(['category', 'unit', 'media' => function($query) {
                $query->orderBy('order');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'ingredients' => $product->ingredients,
                    'price' => $product->price,
                    'final_price' => $product->final_price,
                    'formatted_price' => '$' . number_format($product->final_price, 2, ',', '.'),
                    'has_discount' => $product->hasDiscount(),
                    'discount_percentage' => $product->discount_percentage,
                    'category' => $product->category ? $product->category->name : null,
                    'unit' => $product->unit ? $product->unit->name : null,
                    'images' => $product->media->where('file_type', 'image')->pluck('file_path')->map(function($path) {
                        return asset('storage/' . $path);
                    }),
                    'videos' => $product->media->where('file_type', 'video')->pluck('file_path')->map(function($path) {
                        return asset('storage/' . $path);
                    }),
                ];
            })
        ]);
    }

    /**
     * Obtener categorías activas
     */
    public function categories()
    {
        $categories = \App\Models\Category::where('active', true)
            ->withCount(['products' => function($query) {
                $query->where('active', true)->where('no_stock', false);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                    'products_count' => $category->products_count,
                ];
            })
        ]);
    }

    /**
     * Obtener banners activos
     */
    public function banners()
    {
        $banners = \App\Models\Banner::where('active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'banners' => $banners->map(function($banner) {
                return [
                    'id' => $banner->id,
                    'name' => $banner->name,
                    'image' => asset('storage/' . $banner->image),
                    'url' => $banner->url,
                    'type' => $banner->type,
                ];
            })
        ]);
    }
}
