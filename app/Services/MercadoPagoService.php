<?php

namespace App\Services;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference;
use MercadoPago\Resources\Payment;

class MercadoPagoService
{
    private $preferenceClient;
    private $paymentClient;

    public function __construct()
    {
        // Configurar credenciales de MercadoPago
        $accessToken = config('services.mercadopago.access_token');
        
        if (!$accessToken) {
            throw new \Exception('MercadoPago access token not configured');
        }

        MercadoPagoConfig::setAccessToken($accessToken);
        
        $this->preferenceClient = new PreferenceClient();
        $this->paymentClient = new PaymentClient();
    }

    /**
     * Crear una preferencia de pago para un pedido
     */
    public function createPreference(array $data): Preference
    {
        $preferenceData = [
            "items" => $data['items'],
            "back_urls" => [
                "success" => route('mercadopago.success'),
                "failure" => route('mercadopago.failure'),
                "pending" => route('mercadopago.pending'),
            ],
            "auto_return" => "approved",
            "external_reference" => $data['external_reference'],
            "notification_url" => route('mercadopago.webhook'),
            "statement_descriptor" => "Salvaje Bar",
        ];

        // Agregar información del comprador si está disponible
        if (isset($data['payer'])) {
            $preferenceData['payer'] = $data['payer'];
        }

        try {
            $preference = $this->preferenceClient->create($preferenceData);
            return $preference;
        } catch (\Exception $e) {
            throw new \Exception('Error creating MercadoPago preference: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información de un pago
     */
    public function getPayment(string $paymentId): ?Payment
    {
        try {
            return $this->paymentClient->get($paymentId);
        } catch (\Exception $e) {
            throw new \Exception('Error getting MercadoPago payment: ' . $e->getMessage());
        }
    }

    /**
     * Formatear items para MercadoPago
     */
    public function formatItems(array $orderItems): array
    {
        return collect($orderItems)->map(function ($item) {
            return [
                "title" => $item['product_name'],
                "quantity" => $item['quantity'],
                "unit_price" => (float) $item['product_price'],
                "currency_id" => "ARS",
            ];
        })->toArray();
    }

    /**
     * Formatear información del pagador
     */
    public function formatPayer(array $customerData): array
    {
        return [
            "name" => $customerData['customer_name'] ?? '',
            "email" => $customerData['email'] ?? '',
            "phone" => [
                "area_code" => "11",
                "number" => $customerData['phone'] ?? ''
            ],
            "address" => [
                "street_name" => $customerData['address'] ?? '',
                "street_number" => "",
                "zip_code" => ""
            ]
        ];
    }

    /**
     * Verificar si el pago es válido
     */
    public function validatePayment(Payment $payment): bool
    {
        return $payment->status === 'approved' && $payment->status_detail === 'accredited';
    }

    /**
     * Obtener URL de pago según el medio de pago preferido
     */
    public function getPaymentUrl(Preference $preference, string $method = 'link'): string
    {
        switch ($method) {
            case 'qr':
                return $preference->sandbox_init_point ?? $preference->init_point;
            case 'wallet':
                return $preference->sandbox_init_point ?? $preference->init_point;
            case 'link':
            default:
                return $preference->sandbox_init_point ?? $preference->init_point;
        }
    }
}
