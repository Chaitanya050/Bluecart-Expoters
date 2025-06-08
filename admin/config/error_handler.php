<?php
/**
 * Error Handling Functions
 */

/**
 * Custom error handler
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'time' => date('Y-m-d H:i:s')
    ];
    
    // Log the error
    error_log(json_encode($error));
    
    // Handle different error types
    switch ($errno) {
        case E_USER_ERROR:
            die("<b>ERROR</b> [$errno] $errstr<br />
                 Fatal error on line $errline in file $errfile<br />
                 Exiting.");
            break;
        
        case E_USER_WARNING:
            echo "<b>WARNING</b> [$errno] $errstr<br />
                  Warning on line $errline in file $errfile<br />
                  Continue execution...";
            break;
            
        case E_USER_NOTICE:
            echo "<b>NOTICE</b> [$errno] $errstr<br />
                  Notice on line $errline in file $errfile<br />
                  Continue execution...";
            break;
            
        default:
            echo "Unknown error type: [$errno] $errstr<br />
                  Error on line $errline in file $errfile<br />
                  Continue execution...";
            break;
    }
    
    return true;
}

/**
 * Custom exception handler
 * @param Exception $e Exception object
 */
function customExceptionHandler($e) {
    $error = [
        'type' => 'Exception',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'time' => date('Y-m-d H:i:s')
    ];
    
    // Log the exception
    error_log(json_encode($error));
    
    // Show user-friendly error message
    echo "<div class='alert alert-danger'>
            An error occurred. Please try again later.
            <br>
            Error has been logged for review.
          </div>";
}

/**
 * Custom shutdown handler
 */
function customShutdownHandler() {
    $error = error_get_last();
    if ($error !== null) {
        customErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }
}

/**
 * Initialize error handlers
 */
function initializeErrorHandlers() {
    // Set error reporting level
    error_reporting(E_ALL);
    
    // Set display errors
    ini_set('display_errors', 1);
    
    // Set custom error handler
    set_error_handler('customErrorHandler');
    
    // Set custom exception handler
    set_exception_handler('customExceptionHandler');
    
    // Set custom shutdown handler
    register_shutdown_function('customShutdownHandler');
}

// Initialize error handlers
initializeErrorHandlers();
