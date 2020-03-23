<?php

require('../vendor/autoload.php');
get
$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
// $app->register(new Silex\Provider\TwigServiceProvider(), array(
//     'twig.path' => __DIR__.'/views',
// ));

// Our web handlers

$app->get('/', function() use($app) {
  // $app['monolog']->addDebug('logging output.');
  // return $app['twig']->render('index.twig');
  return "hello world";
});
$app->post('/bot', function() use($app) {
    $data = json_decode(file_get_contents('php://input'));

    if(!$data)
        return 'nioh';

    if(!$data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation' )
        return 'nioh';

    switch( $data->type )
    {
        case 'confirmation':
            return getenv('VK_CONFIRMATION_CODE');
            break;
        case 'massage_new':
            // code...
            break;
    }

    return "nioh";
  });
$app->run();
