<?php

require('../vendor/autoload.php');
//подключаемые библиотеки
use FormulaParser\FormulaParser;//-----

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
            // code...//создание сообщения для отправки
            $request_params = array(
                'user_id' => $data->object->user_id, //id кому отправляет
                'message' => 'Ay',                   //текст который отправляет
                'access_token' => getenv('VK_TOKEN'),
                'v' => '5.69'
            );
//-------
            //формулы
            $formula = $data->object->body;//запись в переменную текста из сообщения
            $precision = 2; // Number of digits after the decimal point

            $parser = new FormulaParser($formula, $precision);
            $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]//запись ответа
        if($result[0]=='done'){                                              //если все ок то след строка записывает в ответ решение
        $request_params['message'] = $formula . '=' . number_format($result['1'], $precision,'.',' ');
        }else {
//-------
            $request_params['message'] = 'Я умею решать примеры и еще кое что;)';
            switch ($data->object->body) {
              case 'Привет':
              case 'привет'
case 'хай':
case 'Хай':
case 'салам':
case 'Салам':
              case 'Hello':
              case 'Hi':
            $fifi=array(
                'user_id' => $data->object->user_id, //id кому отправляет
                'access_token' => getenv('VK_TOKEN'),
                'v' => '5.69'
            );
            $user_info = json_decode(file_get_contents('https://api.vk.com/method/users.get?'.http_build_query($fifi)));
            $qwerty=$user_info->response[0]->first_name;
                  $request_params['message'] = 'Привет, '.$qwerty;
                // code...
              break;
              case 'Пока':
              case 'пока':
              ////
              $tok=getenv('VK_TOKEN');
              $nam=json_decode(file_get_contents("https://api.vk.com/method/users.get?user_id={$data->object->user_id}&access_token={$tok}&v=5.69"))->response[0]->first_name;
                $request_params['message'] =
                'пока, '.$nam;
              break;
              case 'молодец':
$request_params['message'] ='спасибо'
break;


              default:
                // code...
                break;
            }

        }


//-------
            //отсылка сообщения от бота
            file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

            return 'ok';
            // code_end...
            break;
    }

    return "nioh";
  });

$app->run();
