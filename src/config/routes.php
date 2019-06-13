<?php

use App\Utils\File;
use Ramsey\Uuid\Uuid;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    /**
     * POST : /pdf/info
     * DATA : {
     *      pdf: "base64 pdf......"
     * }
     */
    $app->post('/pdf/info', function(Request $request, Response $response) use ($container) {
        $data = $request->getParams();
        $file = new File($data['pdf']);

        $mng = $container->get('pdf');

        $listFields = $mng->listFields($file->getPath());
        $file->clear();
        return $response->withJson(
            $listFields
        );

    });


    /**
     * POST : /pdf[format=json]
     * DATA : {
     *      pdf: "base64 pdf......",
     *      fields : [
     *          "name": "value",
     *          "name": "value"
     *      ]
     * }
     */
    $app->post('/pdf', function(Request $request, Response $response) use ($container) {
        $data = $request->getParams();

        $filename = (isset($data['filename']) && mb_strlen($data['filename']) > 3) ? $data['filename'] : 'downloaded';
        $file = new File($data['pdf']);

        $mng = $container->get('pdf');

        $rawPdf = $mng->fillPdf($file->getPath(), $data['fields']);
        $file->clear();
        return $response->withHeader('Content-type', 'application/pdf')
            ->withHeader('Content-Disposition', sprintf('attachment;filename=%s.pdf', $filename))
            ->write($rawPdf);
    });

    /**
     * POST : /pdf/json
     * DATA : {
     *      pdf: "base64 pdf......",
     *      fields : [
     *          "name": "value",
     *          "name": "value"
     *      ]
     * }
     */
    $app->post('/pdf/json', function(Request $request, Response $response) use ($container) {
        $data = $request->getParams();

        $filename = (isset($data['filename']) && mb_strlen($data['filename']) > 3) ? $data['filename'] : 'downloaded';
        $file = new File($data['pdf']);


        $mng = $container->get('pdf');

        // Generate an unique ID
        $key = Uuid::uuid4()->toString();

        $pdf = $mng->fillPdf($file->getPath(), $data['fields']);

        $redisData = serialize([
           'filename' => $filename,
           'data' => $pdf
        ]);

        /** @var Redis $redis */
        $redis = $container->get('redis');
        $redis->setex($key, 3600, $redisData);
        $file->clear();
        return $response->withJson([
            'url' => sprintf('/pdf/download/%s', $key)
        ]);

    });


    /**
     * GET : /download/{id}
     */
    $app->get('/pdf/download/{pdf}', function (Request $request, Response $response, $args) use ($container) {
        /** @var Redis $redis */
        $redis = $container->get('redis');

        if(!$redis->exists($args['pdf'])) {
            return $response->withStatus(404);
        }

        $pdfData = unserialize($redis->get($args['pdf']));

        return $response->withHeader('Content-type', 'application/pdf')
            ->withHeader('Content-Disposition', sprintf('attachment;filename=%s.pdf', $pdfData['filename']))
            ->write($pdfData['data']);

    });
};
