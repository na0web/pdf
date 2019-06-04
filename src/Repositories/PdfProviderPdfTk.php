<?php


namespace App\Repositories;


use App\Contracts\IPdfProvider;
use App\Contracts\Placeholder;
use Exception;
use mikehaertl\pdftk\Pdf;
use Psr\Container\ContainerInterface;
use Slim\App;

class PdfProviderPdfTk implements IPdfProvider
{
    /** @var array */
    private $placeholders;

    public function __construct(array $placeholders = [])
    {
        $this->placeholders = $placeholders;
    }

    /**
     * @param string $path
     * @param array $fields
     * @return string
     * @throws Exception
     */
    public function fillPdf(string $path, array $fields) : string {
        $pdf = new Pdf($path);
        $fields = $this->mapPlaceholders($path, $fields);
        $pdf->fillForm($fields)->execute();
        if (!($contents = file_get_contents((string)$pdf->getTmpFile()))) {
            throw new Exception('Cannot get tmp pdf file');
        }
        return $contents;
    }

    /**
     * @param string $path
     * @return array
     */
    public function listFields(string $path): array {
        $pdf = new Pdf($path);

        return array_reduce($pdf->getDataFields()->getArrayCopy(), function($acc, $field) {
            $acc[$field['FieldName']] = $field;
            return $acc;
        }, []);
    }

    /**
     * @param string $path
     * @param array $userFields
     * @return array
     */
    private function mapPlaceholders(string $path, array $userFields) : array {
        $pdfFields = $this->listFields($path);
        return array_reduce($pdfFields, function ($carry, $field) use (&$userFields, &$placeholders) {
            $field_split = explode('|', $field['FieldName']);
            if (isset($userFields[$field['FieldName']])) {
                $carry[$field['FieldName']] = $userFields[$field['FieldName']];
            }
            else if (array_key_exists($field_split[0], $this->placeholders)) {
                /** @var Placeholder $placeholder */
                $placeholder = new $this->placeholders[$field_split[0]]();
                $placeholder->parse($field['FieldName']);
                $carry[$field['FieldName']] = $placeholder->execute();
            }
            return $carry;
        }, []);
    }
}