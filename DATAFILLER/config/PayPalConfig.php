<?php
namespace App\Config;

class PayPalConfig {
    private static $clientId = null;
    private static $clientSecret = null;
    private static $environment = null;
    
    /**
     * Obtener Client ID de PayPal desde variables de entorno
     */
    public static function getClientId() {
        if (self::$clientId === null) {
            // Prioridad: Variable de entorno → Fallback local para desarrollo
            self::$clientId = self::getEnvValue('PAYPAL_CLIENT_ID', 'AegRaM9fGG88DaPr0FxesDItgSSzf7466v9COO99qEjwSiissrWNgIMRRxFlyLgS73qWj8Op7eGGc6bf');
        }
        return self::$clientId;
    }
    
    /**
     * Obtener Client Secret de PayPal
     */
    public static function getClientSecret() {
        if (self::$clientSecret === null) {
            self::$clientSecret = self::getEnvValue('PAYPAL_CLIENT_SECRET', 'EDh6ZHHOOpFBiYs2Y4TN4OVzePQnt5u3iYn19OIX5c-6aXqGUK01NVROqPjMnpELAatYYazJk9m7gWs1');
        }
        return self::$clientSecret;
    }
    
    /**
     * Obtener entorno de PayPal (sandbox o live)
     */
    public static function getEnvironment() {
        if (self::$environment === null) {
            // En producción usará 'live', en desarrollo 'sandbox'
            self::$environment = self::getEnvValue('PAYPAL_ENVIRONMENT', 'sandbox');
        }
        return self::$environment;
    }
    
    /**
     * Verificar si estamos en producción
     */
    public static function isProduction() {
        return self::getEnvironment() === 'live';
    }
    
    /**
     * Obtener URL base de PayPal según el entorno
     */
    public static function getApiUrl() {
        return self::isProduction() 
            ? 'https://api.paypal.com'
            : 'https://api.sandbox.paypal.com';
    }
    
    /**
     * Obtener configuración para JavaScript SDK
     */
    public static function getJavaScriptConfig() {
        return [
            'client_id' => self::getClientId(),
            'currency' => 'USD',
            'environment' => self::getEnvironment()
        ];
    }
    
    /**
     * Helper para obtener variables de entorno
     */
    private static function getEnvValue($key, $default = '') {
        // Prioridad: $_ENV → getenv() → default
        if (isset($_ENV[$key]) && !empty($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        $val = getenv($key);
        if ($val !== false && $val !== null && $val !== '') {
            return $val;
        }
        
        return $default;
    }
}
?>
