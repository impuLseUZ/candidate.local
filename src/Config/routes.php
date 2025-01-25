<?php

use App\Routes\GetTestInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return static function (App $app): void {
    $app->get('/api/upload-ads-file', function (Request $request, Response $response) {
        $filePath = '/var/excel/ads.xlsx';
        $data = file_exists($filePath)
            ? ['message' => 'File loaded successfully']
            : ['error' => 'File not found 11'];

        $status = file_exists($filePath) ? 200 : 404;
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    });
};