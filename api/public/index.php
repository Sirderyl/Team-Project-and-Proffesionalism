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

$app->get('/greetings[/{language}]', function (Request $request, Response $response, array $args) {
    $greetings = $this->get(App\Greetings::class);
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

$app->get('/managerSchedule', function (Request $request, Response $response, array $args) {
    $schedule = $this->get(App\Scheduler::class);
    $data = $schedule->getManagerSchedule();
    $body = json_encode($data, JSON_PRETTY_PRINT);
    $response->getBody()->write($body);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/user/register', function (Request $request, Response $response, array $args) {
    $register = new Register();
    $body = $register->parseBody($request->getBody()->getContents());

});

$app->setBasePath('/api');
$app->addErrorMiddleware(true, true, true);

$app->run();
