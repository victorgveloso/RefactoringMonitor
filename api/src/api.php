<?php

    require_once "php-jwt/JWT.php";
    require_once "php-jwt/SignatureInvalidException.php";
    require_once "php-jwt/ExpiredException.php";
    require_once "php-jwt/BeforeValidException.php";
    require_once 'PHPMailer/PHPMailerAutoload.php';
    require_once 'sql.php';
    require_once 'params.php';
    ini_set('memory_limit', '1024M');
    // Enable below lines for MySQL database connection
    // $DATABASE_NAME = getenv('MYSQL_DATABASE') ?: 'refactoring';
    // $DATABASE_HOST = getenv('MYSQL_HOST') ?: 'mysql';
    // $DATABASE_USER = getenv('MYSQL_USER') ?: 'myuser';
    // $DATABASE_PASSWORD = getenv('MYSQL_PASSWORD') ?: 'mypassword';
    // $globalConnection = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASSWORD, $DATABASE_NAME);
    // $connection = new mysqli("db-user-public-my51.encs.concordia.ca", "refactor_admin", "dud4M8G$6y54", "refactoring");
    // $connection = new mysqli("127.0.0.1", "davood", "123456", "lambda-study");
    
    // Define SQLite database path
    $DATABASE_PATH = __DIR__ . '/RefactoringBKP.db';
    
    // Create PDO connection to SQLite database
    try {
        $globalConnection = new PDO('sqlite:' . $DATABASE_PATH);
        $globalConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
    const AllLambdas = 0;
    const OnlyEmailedLambdas = 1;
    const ImInvolvedInLambdas = 2;
    const OnlyEmailedByOthersLambdas = 3;

    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json");

    $paramsProcessor = new Projects(new Lambdas(new EmailedLambdas(new MonitorProject(new SkipLambda(new AllTags(new TagsFor(new SetTag(new GetEmailTemplate(new SendEmail(new AddResponse(new GetEmails(new Login(new Signup(new Refactorings(new GetEmailTemplateRefactoring(new EmailedRefactorings(new CodeRange(new AddResponseRefactoring()))))))))))))))))));
    $paramsProcessor->handle();
   
?>
