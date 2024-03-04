<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container;

AppFactory::setContainer($container);

$app = AppFactory::create();

// TODO: Settings class for database path
$connection = new App\Database\SqliteConnection("database.db");
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

$app->setBasePath('/api');
$app->addErrorMiddleware(true, true, true);

$app->run();
