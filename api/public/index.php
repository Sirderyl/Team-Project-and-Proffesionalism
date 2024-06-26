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

$app->get('/recommendedActivities/{userId}', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\Scheduler::class, ['database' => $database]);
    $data = $handler->getRecommendedActivities(intval($args['userId']));
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/userSchedule/{userId}', function (Request $request, Response $response, array $args) use ($database) {
    $startParam = $request->getQueryParams()['start'] ?? null;
    $endParam = $request->getQueryParams()['end'] ?? null;

    $start = $startParam ? new DateTime($startParam) : null;
    $end = $endParam ? new DateTime($endParam) : null;

    $data = $database->users()->getAssignedActivities(intval($args['userId']), $start, $end);
    $body = json_encode($data, JSON_PRETTY_PRINT);
    $response->getBody()->write($body);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/userSchedule/rating', function (Request $request, Response $response, array $args) use ($database) {
    $rowid = $request->getQueryParams()['id'];
    $rating = $request->getQueryParams()['rating'];

    $database->activities()->setRating(intval($rowid), intval($rating));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/devOrganizationRatings', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\Scheduler::class, ['database' => $database]);
    $data = $handler->getOrganizationRatings();
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/invites/{id}', function (Request $request, Response $response, array $args) use ($database) {
    $data = $database->users()->getInvites(intval($args['id']));
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

$app->get('/user/all', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\GetAllUsers::class, ['database' => $database]);
    $data = $handler->getAllUsers();
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/user/{id}', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\UserEndpoint::class, ['database' => $database]);
    $data = $handler->getUser(intval($args['id']));
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/user/{id}/organizations', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\UserOrganizationsEndpoint::class, ['database' => $database]);
    $data = $handler->execute(intval($args['id']));
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/user/{id}/profilepicture', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ProfilePicture::class, ['database' => $database]);
    [$contentType, $data] = $handler->executeGet(intval($args['id']));
    $response->getBody()->write($data);
    return $response->withHeader('Content-Type', $contentType);
});
$app->post('/user/{id}/profilepicture', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ProfilePicture::class, ['database' => $database]);
    $id = intval($args['id']);
    App\Token::checkAuthMatchesUser($request->getHeader('Authorization')[0], $id);
    $handler->executePost($id, $request->getBody()->getContents());
    return $response->withStatus(204);
});
$app->delete('/user/{id}/profilepicture', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ProfilePicture::class, ['database' => $database]);
    $id = intval($args['id']);
    App\Token::checkAuthMatchesUser($request->getHeader('Authorization')[0], $id);
    $handler->executeDelete($id);
    return $response->withStatus(204);
});


$app->get('/user/{id}/availability', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $data = $handler->getAvailability(intval($args['id']));
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/user/{id}/availability', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $handler->addAvailability(intval($args['id']), $request->getParsedBody());
    return $response->withStatus(201);
});

$app->post('/user/{id}/availability/update', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $handler->updateAvailability(intval($args['id']), $request->getParsedBody());
    return $response->withStatus(200);
});

$app->delete('/user/{id}/availability/{day}', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\AvailabilityEndpoint::class, ['database' => $database]);
    $handler->deleteAvailability(intval($args['id']), App\DayOfWeek::from($args['day']));
    return $response->withStatus(200);
});

$app->get('/organization/{id}/schedule', function (Request $request, Response $response, array $args) use ($container, $database) {
    $startParam = $request->getQueryParams()['start'] ?? null;
    $endParam = $request->getQueryParams()['end'] ?? null;

    $start = $startParam ? new DateTime($startParam) : null;
    $end = $endParam ? new DateTime($endParam) : null;

    $data = $database->organizations()->getActivitySchedule(intval($args['id']), $start, $end);
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/organization/{id}/user/{userId}/status', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\UpdateManagerForm::class, ['database' => $database]);
    $status = $handler->getUserStatus(intval($args['id']), intval($args['userId']));
    $response->getBody()->write(json_encode([
        "status" => $status
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/organization/{id}/user/{userId}/status', function (Request $request, Response $response, array $args) use ($container, $database) {
    $orgId = intval($args['id']);
    $organiztion = $database->organizations()->get($orgId);
    $status = $request->getQueryParams()['status'];
    // TODO: Verify the manager is logged in

    $handler = $container->make(App\UpdateManagerForm::class, ['database' => $database]);
    $handler->setUserStatus($orgId, intval($args['userId']), App\UserOrganizationStatus::from($status));

    return $response->withStatus(201);
});

$app->post('/user/sendNotification', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\MailerEndpoint::class, ['mailer' => $container->get(App\Mailer::class)]);
    $response->getBody()->write($handler->sendEmail($request->getParsedBody()));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/activities', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ActivitiesEndpoint::class, ['database' => $database]);
    $data = $handler->getAll();
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/activity/{id}/userSignup', function (Request $request, Response $response, array $args) use ($container, $database) {
    $handler = $container->make(App\ActivitiesEndpoint::class, ['database' => $database]);
    $handler->assignToUser(intval($args['id']), $request->getParsedBody());
    return $response->withStatus(201);
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
