<?php
namespace App\Middleware;

use App\Config\ApplicationInsights;
use App\Helpers\TelemetryHelper;

class TelemetryMiddleware {
    
    /**
     * Inicializar telemetría automática para todas las páginas
     */
    public static function initialize() {
        // Solo inicializar una vez por request
        if (defined('TELEMETRY_INITIALIZED')) {
            return;
        }
        define('TELEMETRY_INITIALIZED', true);
        
        // Trackear vista de página automáticamente
        self::trackPageView();
        
        // Configurar handler de errores
        self::setupErrorHandling();
        
        // Trackear información de la sesión
        self::trackSessionInfo();
    }
    
    /**
     * Trackear vista de página automáticamente
     */
    private static function trackPageView() {
        $pageName = self::getPageName();
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        
        $properties = [
            'page' => $pageName,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'referrer' => $_SERVER['HTTP_REFERER'] ?? 'direct',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        // Agregar información del usuario si está logueado
        if (isset($_SESSION['usuario'])) {
            $properties['user_id'] = (string)$_SESSION['usuario']['id'];
            $properties['user_name'] = $_SESSION['usuario']['nombre'];
            $properties['user_plan'] = $_SESSION['usuario']['plan'] ?? 'unknown';
        }
        
        ApplicationInsights::trackPageView($pageName, $url, null, $properties);
    }
    
    /**
     * Configurar manejo automático de errores
     */
    private static function setupErrorHandling() {
        // Handler para errores fatales
        register_shutdown_function(function() {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                TelemetryHelper::trackError(
                    $error['message'],
                    [
                        'error_type' => 'fatal_error',
                        'file' => $error['file'] ?? 'unknown',
                        'line' => $error['line'] ?? 0,
                        'page' => self::getPageName()
                    ],
                    $_SESSION['usuario']['id'] ?? null
                );
            }
        });
        
        // Handler para excepciones no capturadas
        set_exception_handler(function($exception) {
            ApplicationInsights::trackException($exception, [
                'page' => self::getPageName(),
                'user_id' => $_SESSION['usuario']['id'] ?? 'anonymous'
            ]);
        });
    }
    
    /**
     * Trackear información de la sesión
     */
    private static function trackSessionInfo() {
        // Solo trackear una vez por sesión
        if (!isset($_SESSION['telemetry_session_tracked'])) {
            ApplicationInsights::trackEvent('SessionStarted', [
                'session_id' => session_id(),
                'user_id' => $_SESSION['usuario']['id'] ?? 'anonymous',
                'page' => self::getPageName(),
                'timestamp' => date('c')
            ]);
            
            $_SESSION['telemetry_session_tracked'] = true;
        }
    }
    
    /**
     * Obtener nombre de la página actual
     */
    private static function getPageName() {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $pathInfo = pathinfo($script);
        return $pathInfo['filename'] ?? 'unknown';
    }
    
    /**
     * Trackear evento de salida (llamar antes de redirects)
     */
    public static function trackPageExit($destination = null) {
        ApplicationInsights::trackEvent('PageExit', [
            'from_page' => self::getPageName(),
            'destination' => $destination,
            'user_id' => $_SESSION['usuario']['id'] ?? 'anonymous',
            'timestamp' => date('c')
        ]);
    }
}
?>