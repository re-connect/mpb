<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminExportService
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * @param array<int, array<int, string|null>> $data
     * @param array<int, string>                  $fields
     */
    public function export(array $data, array $fields): StreamedResponse
    {
        $response = new StreamedResponse();
        $translatedFields = array_map(fn ($field) => $this->translator->trans($field), $fields);
        $response->setCallback(function () use ($data, $translatedFields) {
            $file = fopen('php://output', 'w+');
            if (!$file) {
                return;
            }
            fputcsv($file, $translatedFields, ';');
            foreach ($data as $datum) {
                fputcsv($file, $datum, ';');
            }
            fclose($file);
        });

        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }
}
