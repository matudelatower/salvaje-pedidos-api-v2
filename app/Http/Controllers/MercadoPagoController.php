<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoController extends Controller
{
    private $mercadopagoService;

    public function __construct(MercadoPagoService $mercadopagoService)
    {
        $this->mercadopagoService = $mercadopagoService;
    }

    /**
     * Crear preferencia de pago para un pedido
     */
    public function createPreference(Order $order)
    {
        try {
            // Verificar que el pedido esté en estado pendiente
            if ($order->payment_status !== 'pending') {
                return response()->json([
                    'error' => 'Este pedido ya tiene un pago procesado'
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
                'preference_id' => $preference->id,
                'payment_url' => $this->mercadopagoService->getPaymentUrl($preference),
                'items' => $items,
                'total' => $order->formatted_total,
            ]);

        } catch (\Exception $e) {
            Log::error('MercadoPago preference creation failed: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Error al crear la preferencia de pago',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para recibir notificaciones de MercadoPago
     */
    public function webhook(Request $request)
    {
        Log::info('MercadoPago webhook received', $request->all());

        try {
            $type = $request->input('type');
            $data = $request->input('data');

            if ($type === 'payment') {
                $paymentId = $data['id'];
                $payment = $this->mercadopagoService->getPayment($paymentId);

                if ($payment) {
                    $this->processPaymentNotification($payment);
                }
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('MercadoPago webhook error: ' . $e->getMessage());
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Procesar notificación de pago
     */
    private function processPaymentNotification($payment)
    {
        $externalReference = $payment->external_reference;
        
        if (str_starts_with($externalReference, 'order_')) {
            $orderId = str_replace('order_', '', $externalReference);
            $order = Order::find($orderId);

            if ($order) {
                $this->updateOrderPaymentStatus($order, $payment);
            }
        }
    }

    /**
     * Actualizar estado de pago del pedido
     */
    private function updateOrderPaymentStatus(Order $order, $payment)
    {
        $paymentStatus = 'pending';
        $mercadopagoPaymentId = $payment->id;

        switch ($payment->status) {
            case 'approved':
                if ($this->mercadopagoService->validatePayment($payment)) {
                    $paymentStatus = 'paid';
                }
                break;
            case 'rejected':
            case 'cancelled':
                $paymentStatus = 'failed';
                break;
            case 'in_process':
                $paymentStatus = 'pending';
                break;
        }

        $order->update([
            'payment_status' => $paymentStatus,
            'mercadopago_payment_id' => $mercadopagoPaymentId,
        ]);

        Log::info("Order {$order->id} payment status updated to {$paymentStatus}");
    }

    /**
     * Página de éxito
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $externalReference = $request->get('external_reference');
        
        if ($externalReference && str_starts_with($externalReference, 'order_')) {
            $orderId = str_replace('order_', '', $externalReference);
            $order = Order::find($orderId);
            
            if ($order) {
                return redirect()->route('orders.show', $order)
                    ->with('success', '¡Pago realizado exitosamente! Tu pedido está siendo procesado.');
            }
        }

        return redirect()->route('orders.index')
            ->with('success', '¡Pago realizado exitosamente!');
    }

    /**
     * Página de fallo
     */
    public function failure(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $externalReference = $request->get('external_reference');
        
        if ($externalReference && str_starts_with($externalReference, 'order_')) {
            $orderId = str_replace('order_', '', $externalReference);
            $order = Order::find($orderId);
            
            if ($order) {
                // Enviar mensaje de WhatsApp sobre pago pendiente
                return redirect()->route('orders.show', $order)
                    ->with('warning', 'El pago no pudo completarse. Por favor, intenta nuevamente o contacta con nosotros.');
            }
        }

        return redirect()->route('orders.index')
            ->with('error', 'El pago no pudo completarse. Por favor, intenta nuevamente.');
    }

    /**
     * Página de pago pendiente
     */
    public function pending(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $externalReference = $request->get('external_reference');
        
        if ($externalReference && str_starts_with($externalReference, 'order_')) {
            $orderId = str_replace('order_', '', $externalReference);
            $order = Order::find($orderId);
            
            if ($order) {
                return redirect()->route('orders.show', $order)
                    ->with('info', 'Tu pago está siendo procesado. Te notificaremos cuando se complete.');
            }
        }

        return redirect()->route('orders.index')
            ->with('info', 'Tu pago está siendo procesado.');
    }

    /**
     * Reintentar pago para un pedido
     */
    public function retryPayment(Order $order)
    {
        try {
            if ($order->payment_status === 'paid') {
                return back()->with('error', 'Este pedido ya está pagado.');
            }

            // Crear nueva preferencia
            $response = $this->createPreference($order);
            $preference = json_decode($response->getContent(), true);
            $paymentUrl = $preference['payment_url'];

            return redirect()->away($paymentUrl);

        } catch (\Exception $e) {
            Log::error('Payment retry failed: ' . $e->getMessage());
            
            return back()->with('error', 'No se pudo procesar el pago. Por favor, intenta nuevamente.');
        }
    }
}
