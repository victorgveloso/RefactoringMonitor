<?php

    require_once "php-jwt/JWT.php";
    require_once "php-jwt/SignatureInvalidException.php";
    require_once "php-jwt/ExpiredException.php";
    require_once "php-jwt/BeforeValidException.php";
    require_once 'PHPMailer/PHPMailerAutoload.php';
    require_once 'sql.php';
    require_once 'params.php';
    ini_set('memory_limit', '1024M');
    set_error_handler(function(int $errno, string $errstr) {
        if ((strpos($errstr, 'Undefined array key') === false) && (strpos($errstr, 'Undefined variable') === false)) {
            return false;
        } else {
            return true;
        }
    }, E_WARNING);
    
    // Define SQLite database path
    $DATABASE_PATH = __DIR__ . '/db/refactoringBKP.db';
    
    // Create SQLite3 connection
    try {
        $globalConnection = new SQLite3($DATABASE_PATH);
	$globalConnection->loadExtension('libsqlite_hashes.so');
        $globalConnection->enableExceptions(true);
    } catch (Exception $e) {
        die('Connection failed: ' . $e->getMessage());
    }
    
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json");

    $paramsProcessor = new Projects(new MonitorProject(new AllTags(new TagsFor(new SetTag(new SendEmail(new GetEmails(new Login(new Signup(new Refactorings(new GetEmailTemplateRefactoring(new EmailedRefactorings(new CodeRange(new AddResponseRefactoring())))))))))))));
    $paramsProcessor->handle();
