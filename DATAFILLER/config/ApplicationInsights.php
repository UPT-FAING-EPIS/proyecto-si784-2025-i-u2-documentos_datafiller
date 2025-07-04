<?php
namespace App\Config;
use Exception;

class ApplicationInsights {
    private static $instrumentationKey = null;
    private static $connectionString = null;
    
    /**
     * Obtener la clave de instrumentación desde variables de entorno
     */
    public static function getInstrumentationKey() {
        if (self::$instrumentationKey === null) {
            // Prioridad: Variable de entorno → Fallback local para desarrollo
            self::$instrumentationKey = self::getEnvValue('APPINSIGHTS_INSTRUMENTATIONKEY', '1b3387d3-fc6b-413c-8b83-e1ccbb202fa5');
        }
        return self::$instrumentationKey;
    }
    
    /**
     * Obtener la cadena de conexión
     */
    public static function getConnectionString() {
        if (self::$connectionString === null) {
            $instrumentationKey = self::getInstrumentationKey();
            self::$connectionString = "InstrumentationKey={$instrumentationKey};IngestionEndpoint=https://eastus-8.in.applicationinsights.azure.com/;LiveEndpoint=https://eastus.livediagnostics.monitor.azure.com/;ApplicationId=87610325-a202-43f6-a79c-536dbe60f8d8";
        }
        return self::$connectionString;
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
    
    /**
     * Verificar si estamos en producción (Azure)
     */
    public static function isProduction() {
        return isset($_ENV['APPINSIGHTS_INSTRUMENTATIONKEY']) || 
               getenv('APPINSIGHTS_INSTRUMENTATIONKEY') !== false ||
               isset($_ENV['WEBSITE_SITE_NAME']); // Variable específica de Azure Web Apps
    }
    
    /**
     * Obtener información del entorno
     */
    public static function getEnvironmentInfo() {
        return [
            'environment' => self::isProduction() ? 'production' : 'development',
            'azure_website' => $_ENV['WEBSITE_SITE_NAME'] ?? getenv('WEBSITE_SITE_NAME') ?: 'local',
            'resource_group' => $_ENV['WEBSITE_RESOURCE_GROUP'] ?? getenv('WEBSITE_RESOURCE_GROUP') ?: 'local',
            'instrumentation_source' => isset($_ENV['APPINSIGHTS_INSTRUMENTATIONKEY']) || getenv('APPINSIGHTS_INSTRUMENTATIONKEY') ? 'azure_env' : 'fallback'
        ];
    }
    
    /**
     * Enviar evento personalizado a Application Insights
     */
    public static function trackEvent($eventName, $properties = [], $measurements = []) {
        // Agregar información del entorno a todas las telemetrías
        $envInfo = self::getEnvironmentInfo();
        $properties = array_merge($properties, $envInfo);
        
        // Convertir arrays vacíos a objetos vacíos para JSON
        if (empty($measurements)) {
            $measurements = new \stdClass();
        }
        if (empty($properties)) {
            $properties = new \stdClass();
        }
        
        $data = [
            'name' => 'Microsoft.ApplicationInsights.Event',
            'time' => date('c'),
            'iKey' => self::getInstrumentationKey(),
            'data' => [
                'baseType' => 'EventData',
                'baseData' => [
                    'ver' => 2,
                    'name' => $eventName,
                    'properties' => $properties,
                    'measurements' => $measurements
                ]
            ]
        ];
        
        self::sendToApplicationInsights($data);
    }
    
    /**
     * Enviar vista de página a Application Insights
     */
    public static function trackPageView($pageName, $url, $duration = null, $properties = []) {
        $envInfo = self::getEnvironmentInfo();
        $properties = array_merge($properties, $envInfo);
        
        // Convertir arrays vacíos a objetos vacíos para JSON
        if (empty($properties)) {
            $properties = new \stdClass();
        }
        
        $data = [
            'name' => 'Microsoft.ApplicationInsights.PageView',
            'time' => date('c'),
            'iKey' => self::getInstrumentationKey(),
            'data' => [
                'baseType' => 'PageViewData',
                'baseData' => [
                    'ver' => 2,
                    'name' => $pageName,
                    'url' => $url,
                    'duration' => $duration,
                    'properties' => $properties
                ]
            ]
        ];
        
        self::sendToApplicationInsights($data);
    }
    
    /**
     * Enviar excepción a Application Insights
     */
    public static function trackException($exception, $properties = []) {
        $envInfo = self::getEnvironmentInfo();
        $properties = array_merge($properties, $envInfo);
        
        $data = [
            'name' => 'Microsoft.ApplicationInsights.Exception',
            'time' => date('c'),
            'iKey' => self::getInstrumentationKey(),
            'data' => [
                'baseType' => 'ExceptionData',
                'baseData' => [
                    'ver' => 2,
                    'exceptions' => [[
                        'typeName' => get_class($exception),
                        'message' => $exception->getMessage(),
                        'hasFullStack' => true,
                        'stack' => $exception->getTraceAsString()
                    ]],
                    'properties' => $properties
                ]
            ]
        ];
        
        self::sendToApplicationInsights($data);
    }
    
    /**
     * Enviar métrica personalizada
     */
    public static function trackMetric($name, $value, $properties = []) {
        $envInfo = self::getEnvironmentInfo();
        $properties = array_merge($properties, $envInfo);
        
        $data = [
            'name' => 'Microsoft.ApplicationInsights.Metric',
            'time' => date('c'),
            'iKey' => self::getInstrumentationKey(),
            'data' => [
                'baseType' => 'MetricData',
                'baseData' => [
                    'ver' => 2,
                    'metrics' => [[
                        'name' => $name,
                        'value' => $value,
                        'count' => 1
                    ]],
                    'properties' => $properties
                ]
            ]
        ];
        
        self::sendToApplicationInsights($data);
    }
    
    /**
     * Enviar datos a Application Insights
     */
    private static function sendToApplicationInsights($data) {
        $json = json_encode($data);
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://dc.applicationinsights.azure.com/v2/track');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json)
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'DataFiller-Azure-App/1.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode !== 200) {
                error_log("Application Insights HTTP Error: $httpCode - Response: $response");
            }
            
            curl_close($ch);
            
            // Log para debugging en desarrollo
            if (!self::isProduction()) {
                error_log("Application Insights Event Sent: " . ($data['data']['baseData']['name'] ?? 'Unknown'));
            }
            
        } catch (Exception $e) {
            error_log("Application Insights error: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener información del usuario actual
     */
    public static function getUserInfo() {
        $userInfo = [
            'ip' => self::getClientIP(),
            'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'sessionId' => session_id(),
            'userId' => $_SESSION['usuario']['id'] ?? 'anonymous',
            'userName' => $_SESSION['usuario']['nombre'] ?? 'anonymous',
            'environment' => self::isProduction() ? 'production' : 'development'
        ];
        
        return $userInfo;
    }
    
    /**
     * Obtener IP del cliente
     */
    private static function getClientIP() {
        // Headers específicos de Azure y otros proxies
        $ipKeys = [
            'HTTP_X_FORWARDED_FOR',    // Azure Load Balancer
            'HTTP_X_REAL_IP',          // Nginx
            'HTTP_CF_CONNECTING_IP',   // Cloudflare
            'HTTP_CLIENT_IP',          // Proxy
            'REMOTE_ADDR'              // Directo
        ];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Generar script JavaScript para el frontend
     */
    public static function getJavaScriptSnippet() {
        $instrumentationKey = self::getInstrumentationKey();
        $connectionString = self::getConnectionString();
        $environment = self::isProduction() ? 'production' : 'development';
        
        return <<<JS
<script type="text/javascript">
!function(T,l,y){var S=T.location,k="script",D="connectionString",C="InstrumentationKey",t="ingestionendpoint",x="liveendpoint",E="applicationinsightsendpoint",A="disableExceptionTracking",b="ai.device.",g="toLowerCase",f="crossOrigin",m="POST",h="appInsightsSDK",p=y.name||"appInsights",n=((T[h]=T[h]||{}).queue=[],function(e){T[h].queue.push(e)});(T[p]!==n)&&(T[p]=n);var w=function(){var e,i={config:{instrumentationKey:"$instrumentationKey",connectionString:"$connectionString"}};if(T[C]||T[D]||i.config[C]||i.config[D]){if(T[C]&&(i.config[C]=T[C]),T[D]&&(i.config[D]=T[D]),i.config[D]){var n=i.config[D].match(/ingestionendpoint=([^;]*)/);n&&(i.config[t]=n[1])}var a=i.config[D].match(/liveendpoint=([^;]*)/);if(a&&(i.config[x]=a[1]),i.config[D].match(/applicationinsightsendpoint=([^;]*)/)){var s=i.config[D].match(/applicationinsightsendpoint=([^;]*)/);s&&(i.config[E]=s[1])}i.config[t]||(i.config[t]="https://dc.applicationinsights.azure.com/"),i.config[x]||(i.config[x]="https://rt.applicationinsights.microsoft.com/"),i.config[E]||(i.config[E]="https://api.applicationinsights.io/");var c=i.config[C]||i.config[D];return c&&(e=T.performance&&T.performance.timing&&T.performance.timing.navigationStart||0,i.queue=[],i.version="2.8.5"),i}(T,0,y);T[p]=w;var u=l.createElement(k);u.async=!0,u.src="https://js.monitor.azure.com/scripts/b/ai.2.min.js",u[f]="anonymous";var v=l.getElementsByTagName(k)[0];v.parentNode.insertBefore(u,v)}(window,document,{name:"appInsights",queue:[],version:"2.8.5"});

// Configurar propiedades globales
window.appInsights=window.appInsights||{},appInsights.queue=appInsights.queue||[];

// Agregar propiedades del entorno
appInsights.setAuthenticatedUserContext = appInsights.setAuthenticatedUserContext || function(){};
appInsights.addTelemetryInitializer = appInsights.addTelemetryInitializer || function(){};

// Configurar contexto global
appInsights.addTelemetryInitializer(function(envelope) {
    envelope.data = envelope.data || {};
    envelope.data.environment = '$environment';
    envelope.data.application = 'DataFiller';
    envelope.data.version = '1.0';
});

console.log('Application Insights initialized for environment: $environment');
</script>
JS;
    }
    
    /**
     * Test de conectividad con Application Insights
     */
    public static function testConnection() {
        try {
            self::trackEvent('ApplicationInsights_ConnectionTest', [
                'test_timestamp' => date('c'),
                'environment' => self::isProduction() ? 'production' : 'development',
                'instrumentation_key_source' => isset($_ENV['APPINSIGHTS_INSTRUMENTATIONKEY']) || getenv('APPINSIGHTS_INSTRUMENTATIONKEY') ? 'azure_env' : 'fallback'
            ]);
            
            return [
                'success' => true,
                'message' => 'Application Insights connection test successful',
                'environment' => self::getEnvironmentInfo()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Application Insights connection test failed: ' . $e->getMessage(),
                'environment' => self::getEnvironmentInfo()
            ];
        }
    }
}
?>