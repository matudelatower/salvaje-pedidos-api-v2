<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl;
    private $token;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->token = config('services.whatsapp.token');
    }

    /**
     * Enviar mensaje de confirmación de pedido
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        try {
            $message = $this->generateOrderConfirmationMessage($order);
            return $this->sendMessage($order->phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp order confirmation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de pago pendiente
     */
    public function sendPaymentPending(Order $order): bool
    {
        try {
            $message = $this->generatePaymentPendingMessage($order);
            return $this->sendMessage($order->phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp payment pending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de pedido listo
     */
    public function sendOrderReady(Order $order): bool
    {
        try {
            $message = $this->generateOrderReadyMessage($order);
            return $this->sendMessage($order->phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp order ready failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de pedido entregado
     */
    public function sendOrderDelivered(Order $order): bool
    {
        try {
            $message = $this->generateOrderDeliveredMessage($order);
            return $this->sendMessage($order->phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp order delivered failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje personalizado
     */
    public function sendCustomMessage(string $phone, string $message): bool
    {
        try {
            return $this->sendMessage($phone, $message);
        } catch (\Exception $e) {
            Log::error('WhatsApp custom message failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje usando la API de WhatsApp
     */
    private function sendMessage(string $phone, string $message): bool
    {
        $formattedPhone = $this->formatPhoneNumber($phone);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $formattedPhone,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, $payload);

        if ($response->successful()) {
            Log::info('WhatsApp message sent successfully', [
                'phone' => $formattedPhone,
                'message' => substr($message, 0, 100) . '...'
            ]);
            return true;
        } else {
            Log::error('WhatsApp API error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'phone' => $formattedPhone
            ]);
            return false;
        }
    }

    /**
     * Generar mensaje de confirmación de pedido
     */
    private function generateOrderConfirmationMessage(Order $order): string
    {
        $items = $order->orderItems->map(function($item) {
            return "• {$item->quantity}x {$item->product_name} - {$item->formatted_price}";
        })->implode("\n");

        return "¡Hola {$order->customer_name}! 👋\n\n" .
               "📋 *PEDIDO #{$order->id} RECIBIDO*\n\n" .
               "📦 *Tus productos:*\n{$items}\n\n" .
               "💰 *Total: {$order->formatted_total}*\n" .
               "📍 *Tipo de envío: " . ($order->delivery_type === 'delivery' ? 'Delivery' : 'Retiro en local') . "*\n\n" .
               "✅ *¡Recibimos tu pedido y lo estamos preparando!*\n\n" .
               "🍔 *Salvaje Bar*\n" .
               "📞 ¿Consultas? +54 9 11 XXXX XXXX";
    }

    /**
     * Generar mensaje de pago pendiente
     */
    private function generatePaymentPendingMessage(Order $order): string
    {
        return "¡Hola {$order->customer_name}! 👋\n\n" .
               "⚠️ *PAGO PENDIENTE*\n\n" .
               "📋 *Pedido #{$order->id}*\n" .
               "💰 *Monto: {$order->formatted_total}*\n\n" .
               "Tu pedido está listo pero necesitamos que completes el pago.\n\n" .
               "💳 *Link de pago:*\n" .
               route('orders.mercadopago.retry', $order) . "\n\n" .
               "Si ya pagaste, ignora este mensaje.\n\n" .
               "🍔 *Salvaje Bar*";
    }

    /**
     * Generar mensaje de pedido listo
     */
    private function generateOrderReadyMessage(Order $order): string
    {
        return "¡Hola {$order->customer_name}! 🎉\n\n" .
               "✅ *TU PEDIDO ESTÁ LISTO*\n\n" .
               "📋 *Pedido #{$order->id}*\n" .
               "🍔 *Ya puedes retirarlo!*\n\n" .
               ($order->delivery_type === 'delivery' 
                   ? "🚚 Tu pedido está en camino. Te contactaremos cuando esté cerca.\n\n"
                   : "📍 Ven a retirarlo a nuestro local.\n\n"
               ) .
               "🍔 *Salvaje Bar*\n" .
               "📞 +54 9 11 XXXX XXXX";
    }

    /**
     * Generar mensaje de pedido entregado
     */
    private function generateOrderDeliveredMessage(Order $order): string
    {
        return "¡Hola {$order->customer_name}! 🎉\n\n" .
               "✅ *PEDIDO ENTREGADO*\n\n" .
               "📋 *Pedido #{$order->id}*\n" .
               "💰 *Total: {$order->formatted_total}*\n\n" .
               "¡Esperamos que disfrutes tu pedido!\n\n" .
               "⭐ *Califica nuestra experiencia:* \n" .
               "Nos ayuda a mejorar 💚\n\n" .
               "🍔 *Salvaje Bar*\n" .
               "¡Gracias por tu compra! 🙏";
    }

    /**
     * Formatear número de teléfono para WhatsApp
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Eliminar caracteres no numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Eliminar código de país si existe
        if (str_starts_with($phone, '54')) {
            $phone = substr($phone, 2);
        }
        
        // Eliminar 15 si existe (formato argentino)
        if (str_starts_with($phone, '15')) {
            $phone = substr($phone, 2);
        }
        
        // Agregar código de país y formato correcto
        return '54' . '9' . $phone;
    }

    /**
     * Obtener URL de WhatsApp para enviar mensaje manualmente
     */
    public function getWhatsAppUrl(string $phone, string $message): string
    {
        $formattedPhone = $this->formatPhoneNumber($phone);
        return "https://wa.me/{$formattedPhone}?text=" . urlencode($message);
    }

    /**
     * Verificar si el número de teléfono es válido
     */
    public function isValidPhoneNumber(string $phone): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Verificar que tenga entre 10 y 13 dígitos
        return strlen($phone) >= 10 && strlen($phone) <= 13;
    }
}
