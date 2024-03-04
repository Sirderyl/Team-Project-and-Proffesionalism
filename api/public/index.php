<?php

declare(strict_types = 1);

use App\Database\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container;

AppFactory::setContainer($container);

$app = AppFactory::create();

// TODO: Settings class for database path
$connection = new App\Database\SqliteConnection("../database.db");
$database = new App\Database\Database($connection);

$app->get('/greetings[/{language}]', function (Request $request, Response $response, array $args) use ($container) {
    $greetings = $container->get(App\Greetings::class);
    $language = $args['language'] ?? null;

    if($language !== null){
        $data = $greetings->getGreeting($args['language']);
    }else {
        $data = $greetings->getGreetings();
    }

    $body = json_encode($data, JSON_PRETTY_PRINT);
    $response->getBody()->write($body);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/managerSchedule', function (Request $request, Response $response, array $args) use ($container) {
    $schedule = $container->get(App\Scheduler::class);
    $data = $schedule->getManagerSchedule();
    $body = json_encode($data, JSON_PRETTY_PRINT);
    $response->getBody()->write($body);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/user/register', function (Request $request, Response $response, array $args) use ($container, $database) {
    $register = $container->make(App\Register::class, ['database' => $database]);
    $body = $register->parseBody($request->getBody()->getContents());

    $response->getBody()->write(json_encode($register->execute($body)));
    return $response->withHeader('Content-Type', 'application/json');
});
$app->post('/user/login', function (Request $request, Response $response, array $args) use ($container, $database) {
    $login = $container->make(App\Login::class, ['database' => $database]);

    $response->getBody()->write(json_encode($login->execute()));
    return $response->withHeader('Content-Type', 'application/json');
});


// Allow CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Authorization');
});


function getErrorCode(Throwable $exception): int
{
    if ($exception instanceof NotFoundException) {
        return 404;
    }
    if ($exception instanceof InvalidArgumentException) {
        return 400;
    }

    return 500;
}

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode([
        'error' => $exception->getMessage(),
        'trace' => $exception->getTrace(),
    ]));
    return $response
        ->withStatus(getErrorCode($exception))
        ->withHeader('Content-Type', 'application/json');
});

$app->setBasePath('/api');

$app->run();
