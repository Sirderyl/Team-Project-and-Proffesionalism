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


// Allow CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function (Request $request, RequestHandler $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


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

$app->get('/activity/{id}', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\GetActivity::class, ['database' => $database]);
    $data = $handler->execute(intval($args['id']));
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/activity/{id}/previewimage', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ActivityPicture::class, ['database' => $database]);
    $data = $handler->execute(intval($args['id']));
    $response->getBody()->write($data);
    return $response->withHeader('Content-Type', $handler->getContentType());
});

$app->post('/user/register', function (Request $request, Response $response, array $args) use ($container, $database) {
    $register = $container->make(App\Register::class, ['database' => $database]);
    $body = $register->parseBody($request->getBody()->getContents());

    $response->getBody()->write(json_encode($register->execute($body)));
    return $response->withHeader('Content-Type', 'application/json');
});
$app->post('/user/login', function (Request $request, Response $response, array $args) use ($container, $database) {
    $login = $container->make(App\Login::class, ['database' => $database]);


    $email = $_SERVER['PHP_AUTH_USER'] ?? null;
    $password = $_SERVER['PHP_AUTH_PW'] ?? null;

    $response->getBody()->write(json_encode($login->execute($email, $password)));
    return $response->withHeader('Content-Type', 'application/json');
});
$app->get('/user/{id}/profilepicture', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ProfilePicture::class, ['database' => $database]);
    $data = $handler->execute(intval($args['id']));
    $response->getBody()->write($data);
    return $response->withHeader('Content-Type', $handler->getContentType());
});

$app->get('/user/{id}/availability', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $data = $handler->getAvailability($args['id']);
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/user/{id}/availability', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $handler->addAvailability($args['id'], $request->getParsedBody());
    return $response->withStatus(201);
});

$app->delete('/user/{id}/availability/{day}', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $handler->deleteAvailability($args['id'], $args['day']);
    return $response->withStatus(200);
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
        // Need to duplicate this line because the CORS middleware is not called for errors
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Content-Type', 'application/json');
});

$app->setBasePath('/api');

$app->run();
