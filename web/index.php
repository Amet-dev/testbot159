<?php

require('../vendor/autoload.php');
//подключаемые библиотеки
use FormulaParser\FormulaParser;

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

        case 'message_new':
            // code...
            $request_params = array(
                'user_id' => $data->object->user_id,
                'message' => 'Ay',
                'access_token' => getenv('VK_TOKEN'),
                'v' => '5.69'
            );
            //формулы
            $formula = $data->object->body;
            $precision = 2; // Number of digits after the decimal point
            try {
                $parser = new FormulaParser($formula, $precision);
                $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
        if($result[0]=='done'){
        $request_params['message'] = $formula . '=' . number_format($result['1'], $precision,'.',' ');
        }else {
            $request_params['message'] = 'Я умею решать примеры, если вы написали его неправильно это не моя вина)';
        }
            } catch (\Exception $e) {
                $request_params['message'] = 'что-то сложновато';
            }
            //отсылка сообщения от бота
            file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

            return 'ok';
            // code_end...
            break;
    }

    return "nioh";
  });

$app->run();
