<?php
namespace App\Helpers;

use App\Config\ApplicationInsights;

class TelemetryHelper {
    public static function trackLogin($userId, $userName, $success = true, $errorMessage = null) {
        $properties = [
            'userId' => (string)$userId,
            'userName' => $userName,
            'success' => $success ? 'true' : 'false',
            'ip' => ApplicationInsights::getUserInfo()['ip'],
            'userAgent' => ApplicationInsights::getUserInfo()['userAgent']
        ];
        
        if (!$success && $errorMessage) {
            $properties['errorMessage'] = $errorMessage;
        }
        
        ApplicationInsights::trackEvent('UserLogin', $properties);
        
        // También trackear como métrica
        ApplicationInsights::trackMetric('LoginAttempts', 1, [
            'success' => $success ? 'true' : 'false'
        ]);
    }
    
    /**
     * Trackear acceso a generardata.php
     */
    public static function trackGenerarDataAccess($userId = null, $userName = null) {
        $userInfo = ApplicationInsights::getUserInfo();
        
        $properties = [
            'userId' => $userId ? (string)$userId : $userInfo['userId'],
            'userName' => $userName ?? $userInfo['userName'],
            'ip' => $userInfo['ip'],
            'userAgent' => $userInfo['userAgent'],
            'sessionId' => $userInfo['sessionId']
        ];
        
        ApplicationInsights::trackPageView('GenerarData', $_SERVER['REQUEST_URI'] ?? '/generardata', null, $properties);
        ApplicationInsights::trackEvent('PageAccess_GenerarData', $properties);
    }
    
    /**
     * Trackear análisis de SQL
     */
    public static function trackSqlAnalysis($userId, $tablesCount, $source = 'manual') {
        $properties = [
            'userId' => (string)$userId,
            'source' => $source, // manual, file, github
            'tablesDetected' => (string)$tablesCount
        ];
        
        ApplicationInsights::trackEvent('SqlAnalysis', $properties);
        ApplicationInsights::trackMetric('TablesAnalyzed', $tablesCount, $properties);
    }
    
    /**
     * Trackear uso de GitHub integration
     */
    public static function trackGitHubUsage($userId, $action, $repo = null, $success = true) {
        $properties = [
            'userId' => (string)$userId,
            'action' => $action, // load_repos, load_files, download_file
            'success' => $success ? 'true' : 'false'
        ];
        
        if ($repo) {
            $properties['repository'] = $repo;
        }
        
        ApplicationInsights::trackEvent('GitHubIntegration', $properties);
    }
    
    /**
     * Trackear generación de datos
     */
    public static function trackDataGeneration($userId, $recordCount, $format) {
        $properties = [
            'userId' => (string)$userId,
            'recordCount' => (string)$recordCount,
            'format' => $format
        ];
        
        ApplicationInsights::trackEvent('DataGeneration', $properties);
        ApplicationInsights::trackMetric('RecordsGenerated', $recordCount, $properties);
    }
    
    /**
     * Trackear errores de la aplicación
     */
    public static function trackError($errorMessage, $context = [], $userId = null) {
        $properties = array_merge([
            'userId' => $userId ? (string)$userId : 'anonymous',
            'page' => $_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF'] ?? 'unknown'
        ], $context);
        
        ApplicationInsights::trackEvent('ApplicationError', array_merge($properties, [
            'errorMessage' => $errorMessage
        ]));
    }
    
    /**
     * Trackear registro de nuevos usuarios
     */
    public static function trackUserRegistration($userId, $userName, $email) {
        $properties = [
            'userId' => (string)$userId,
            'userName' => $userName,
            'email' => $email,
            'ip' => ApplicationInsights::getUserInfo()['ip']
        ];
        
        ApplicationInsights::trackEvent('UserRegistration', $properties);
        ApplicationInsights::trackMetric('NewUsers', 1);
    }
    
    /**
     * Trackear acceso a cualquier página del user journey
     */
    public static function trackPageAccess($userId, $userName, $pageName) {
        $userInfo = ApplicationInsights::getUserInfo();
        
        $properties = [
            'userId' => (string)$userId,
            'userName' => $userName,
            'page' => $pageName,
            'ip' => $userInfo['ip'],
            'userAgent' => $userInfo['userAgent'],
            'sessionId' => $userInfo['sessionId']
        ];
        
        ApplicationInsights::trackPageView($pageName, $_SERVER['REQUEST_URI'] ?? "/$pageName", null, $properties);
        ApplicationInsights::trackEvent("PageAccess_$pageName", $properties);
    }
    
    /**
     * Trackear intento de pago
     */
    public static function trackPaymentAttempt($userId, $planId, $planName, $amount) {
        $properties = [
            'userId' => (string)$userId,
            'planId' => (string)$planId,
            'planName' => $planName,
            'amount' => (string)$amount,
            'paymentMethod' => 'paypal'
        ];
        
        ApplicationInsights::trackEvent('PaymentAttempt', $properties);
    }
    
    /**
     * Trackear pago exitoso
     */
    public static function trackPaymentSuccess($userId, $planId, $amount) {
        $properties = [
            'userId' => (string)$userId,
            'planId' => (string)$planId,
            'amount' => (string)$amount,
            'paymentMethod' => 'paypal'
        ];
        
        ApplicationInsights::trackEvent('PaymentSuccess', $properties);
        ApplicationInsights::trackMetric('Revenue', $amount, $properties);
    }
    
    /**
     * Trackear error de pago
     */
    public static function trackPaymentError($userId, $errorMessage) {
        $properties = [
            'userId' => (string)$userId,
            'errorMessage' => $errorMessage,
            'paymentMethod' => 'paypal'
        ];
        
        ApplicationInsights::trackEvent('PaymentError', $properties);
    }
}
?>