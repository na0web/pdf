<?php

use App\Contracts\IPaymentProvider;
use App\Contracts\IPdfProvider;
use App\Repositories\PaymentProviderStripe;
use App\Utils\SubscriptionMatrix;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // redis
    $container['redis'] = function ($c) : Redis {
        $settings = $c->get('settings')['redis'];
        $redis = new Redis();
        $redis->connect($settings['host'], $settings['port']);
        return $redis;
    };

    // Placeholders
    $container['placeholdersClasses'] = function ($c) {
        $settings = $c->get('placeholders');
        return array_reduce($settings, function ($carry, $setting) {
            $placeholder = new $setting;
            $carry[$placeholder->getName()] = $setting;
            return $carry;
        }, []);
    };


    $container['pdf'] = function ($c) : IPdfProvider {
        $settings = $c->get('placeholdersClasses');
        return new \App\Repositories\PdfProviderPdfTk($settings);
    };
};
