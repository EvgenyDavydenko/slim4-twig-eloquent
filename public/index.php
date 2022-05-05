<?php
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Illuminate\Database\Capsule\Manager;
use models\User;

require __DIR__ . '/../vendor/autoload.php';
$settings = require __DIR__ . '/../config/settings.php';

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// Create Container using PHP-DI
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);

// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Add Eloquent ORM
$capsule = new Manager;
$capsule->addConnection($settings['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();


// Add route callbacks
$app->get('/', function (Request $request, Response $response, $args) use ($twig) {
    $users = User::All();
    $body = $twig->render('home.twig.html', ['users' => $users]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) use ($twig) {
    $body = $twig->render('about.twig.html');
    $response->getBody()->write($body);
    return $response;
});

// Run application
$app->run();
