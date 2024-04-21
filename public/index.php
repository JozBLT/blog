<?php
require './vendor/autoload.php';

$renderer = new \Framework\Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');

$app =  new \Framework\App([
    \App\Blog\BlogModule::class
], [
    'renderer' => $renderer
]);

try {
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
} catch (Exception $e) {
}
