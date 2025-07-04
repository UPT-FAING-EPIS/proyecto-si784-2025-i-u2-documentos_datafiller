<?php
namespace App\Controllers;

use App\Config\PayPalConfig;
use App\Config\Database;
use App\Helpers\TelemetryHelper;
use Exception;

class PayPalController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Crear orden de pago en PayPal
     */
    public function createOrder($planId, $userId) {
        try {
            // Debug: Log de parámetros recibidos
            error_log("PayPalController::createOrder - Plan ID: $planId (tipo: " . gettype($planId) . "), User ID: $userId");
            
            // Obtener información del plan
            $plan = $this->getPlanById($planId);
            if (!$plan) {
                error_log("PayPalController::createOrder - Plan no encontrado para ID: $planId");
                throw new Exception("Plan no encontrado para ID: $planId");
            }
            
            error_log("PayPalController::createOrder - Plan encontrado: " . json_encode($plan));
            
            // Obtener token de acceso de PayPal
            $accessToken = $this->getAccessToken();
            
            // Crear orden
            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($plan['precio_mensual'], 2, '.', '')
                    ],
                    'description' => "DataFiller - Plan {$plan['nombre']}",
                    'custom_id' => "{$userId}_{$planId}_" . time()
                ]],
                'application_context' => [
                    'brand_name' => 'DataFiller',
                    'landing_page' => 'NO_PREFERENCE',
                    'user_action' => 'PAY_NOW',
                    'return_url' => $this->getReturnUrl(),
                    'cancel_url' => $this->getCancelUrl()
                ]
            ];
            
            $response = $this->makePayPalRequest('/v2/checkout/orders', 'POST', $orderData, $accessToken);
            
            if ($response && isset($response['id'])) {
                // Guardar orden en base de datos
                $this->saveOrder($response['id'], $userId, $planId, $plan['precio_mensual']);
                
                // ✅ TRACKEAR INTENTO DE PAGO
                TelemetryHelper::trackPaymentAttempt($userId, $planId, $plan['nombre'], $plan['precio_mensual']);
                
                return [
                    'success' => true,
                    'order_id' => $response['id'],
                    'approval_url' => $this->getApprovalUrl($response)
                ];
            }
            
            throw new Exception('Error creando orden en PayPal');
            
        } catch (Exception $e) {
            error_log("Error creating PayPal order: " . $e->getMessage());
            
            // ✅ TRACKEAR ERROR DE PAGO
            TelemetryHelper::trackPaymentError($userId, $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Error procesando el pago: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Capturar pago después de aprobación
     */
    public function captureOrder($orderId) {
        try {
            $accessToken = $this->getAccessToken();
            $response = $this->makePayPalRequest("/v2/checkout/orders/{$orderId}/capture", 'POST', [], $accessToken);
            
            if ($response && $response['status'] === 'COMPLETED') {
                // Actualizar orden en base de datos
                $this->updateOrderStatus($orderId, 'completado');
                
                // Activar plan premium para el usuario
                $order = $this->getOrderById($orderId);
                if ($order) {
                    $this->activatePremiumPlan($order['usuario_id'], $order['plan_id']);
                    
                    // ✅ TRACKEAR PAGO EXITOSO
                    TelemetryHelper::trackPaymentSuccess(
                        $order['usuario_id'], 
                        $order['plan_id'], 
                        $order['monto']
                    );
                }
                
                return [
                    'success' => true,
                    'message' => '¡Pago completado! Tu plan Premium ha sido activado.'
                ];
            }
            
            throw new Exception('Error capturando el pago');
            
        } catch (Exception $e) {
            error_log("Error capturing PayPal order: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error completando el pago: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener token de acceso de PayPal
     */
    private function getAccessToken() {
        $clientId = PayPalConfig::getClientId();
        $clientSecret = PayPalConfig::getClientSecret();
        $apiUrl = PayPalConfig::getApiUrl();
        
        error_log("getAccessToken - API URL: $apiUrl");
        error_log("getAccessToken - Client ID: " . substr($clientId, 0, 20) . "...");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$apiUrl/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US',
            'Authorization: Basic ' . base64_encode("$clientId:$clientSecret")
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("getAccessToken - HTTP Code: $httpCode");
        if ($curlError) {
            error_log("getAccessToken - CURL Error: $curlError");
        }
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            error_log("getAccessToken - Token obtenido exitosamente");
            return $data['access_token'];
        }
        
        error_log("getAccessToken - Error response: $response");
        throw new Exception("Error obteniendo token de PayPal (HTTP $httpCode)");
    }
    
    /**
     * Hacer petición a PayPal API
     */
    private function makePayPalRequest($endpoint, $method = 'GET', $data = [], $accessToken = null) {
        $apiUrl = PayPalConfig::getApiUrl();
        
        error_log("makePayPalRequest - $method $apiUrl$endpoint");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        if ($accessToken) {
            $headers[] = "Authorization: Bearer $accessToken";
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("makePayPalRequest - HTTP Code: $httpCode");
        if ($curlError) {
            error_log("makePayPalRequest - CURL Error: $curlError");
        }
        
        if ($httpCode >= 200 && $httpCode < 300) {
            $result = json_decode($response, true);
            error_log("makePayPalRequest - Success: " . json_encode($result));
            return $result;
        }
        
        error_log("makePayPalRequest - Error response: $response");
        throw new Exception("PayPal API error: HTTP $httpCode - $response");
    }
    
    /**
     * Obtener información del plan
     */
    private function getPlanById($planId) {
        error_log("getPlanById - Buscando plan con ID: $planId (tipo: " . gettype($planId) . ")");
        
        $query = "SELECT * FROM tbplanes WHERE id = :plan_id AND activo = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':plan_id', $planId);
        $stmt->execute();
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        error_log("getPlanById - Resultado: " . ($result ? json_encode($result) : 'NULL'));
        
        return $result;
    }
    
    /**
     * Guardar orden en base de datos
     */
    private function saveOrder($orderId, $userId, $planId, $amount) {
        $query = "INSERT INTO tbpagos (usuario_id, plan_id, monto, metodo_pago, estado_pago, referencia_pago, fecha_vencimiento) 
                  VALUES (:usuario_id, :plan_id, :monto, 'paypal', 'pendiente', :referencia, :fecha_vencimiento)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario_id', $userId);
        $stmt->bindParam(':plan_id', $planId);
        $stmt->bindParam(':monto', $amount);
        $stmt->bindParam(':referencia', $orderId);
        
        $fechaVencimiento = date('Y-m-d', strtotime('+1 month'));
        $stmt->bindParam(':fecha_vencimiento', $fechaVencimiento);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar estado de orden
     */
    private function updateOrderStatus($orderId, $status) {
        $query = "UPDATE tbpagos SET estado_pago = :estado WHERE referencia_pago = :orden_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':estado', $status);
        $stmt->bindParam(':orden_id', $orderId);
        return $stmt->execute();
    }
    
    /**
     * Obtener orden por ID
     */
    private function getOrderById($orderId) {
        $query = "SELECT * FROM tbpagos WHERE referencia_pago = :orden_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':orden_id', $orderId);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Activar plan premium para usuario
     */
    private function activatePremiumPlan($userId, $planId) {
        $query = "UPDATE tbusuario SET tipo_plan = 'premium', fecha_suscripcion = NOW(), consultas_diarias = -1 WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
    
    /**
     * Obtener URL de aprobación
     */
    private function getApprovalUrl($orderResponse) {
        foreach ($orderResponse['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return $link['href'];
            }
        }
        return null;
    }
    
    /**
     * URLs de retorno
     */
    private function getReturnUrl() {
        return "http://" . $_SERVER['HTTP_HOST'] . "/views/Auth/payment_success.php";
    }
    
    private function getCancelUrl() {
        return "http://" . $_SERVER['HTTP_HOST'] . "/views/Auth/payment_cancel.php";
    }
}
?>
