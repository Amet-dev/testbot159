<?php

require('../vendor/autoload.php');

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

    if( !$data )
        return 'nioh';

    if( $data->secret !== getenv('VK_SECRET_TOKEN') && $data->type !== 'confirmation' )
        return 'nioh';

    switch( $data->type )
    {
        case 'confirmation':
            return getenv('VK_CONFIRMATION_CODE');
            break;

        case 'massage_new':
            // code...
            $request_params = array(
                'user_id' => $data->object->from_id,
                'massage' => 'П',
                'access_token' => getenv('VK_TOKEN'),
                'v' => '5.69'
            );

            file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

            return 'ok';
            break;
    }

    return "nioh";
  });

$app->run();
