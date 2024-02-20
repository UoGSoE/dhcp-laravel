<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportCsvService
{
    private Writer $writer;

    public function __construct(
        private Collection|array $data,
        private string $filename,
        private array $dataHeaders,
        private array $dataProperties,
    ) {
        $this->writer = Writer::createFromString('');
    }

    public function exportCsvAsStream(): StreamedResponse
    {
        $csvHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $this->filename . '.csv"',
        ];

        $this->writeCsv();

        return response()->streamDownload(function () {
            echo $this->writer->toString();
        }, "{$this->filename}.csv", $csvHeaders);
    }

    public function writeCsvDataToFile(string $fileName): string
    {
        $this->writeCsv();
        Storage::put($fileName, $this->writer->toString());

        return Storage::path($fileName);
    }

    public function appendCsvDataToFile(string $fileName): string
    {
        $this->writeCsv();
        Storage::append($fileName, $this->writer->toString(), null);

        return Storage::path($fileName);
    }

    private function writeCsv(): void
    {
        if (!empty($this->dataHeaders)) {
            $this->writer->insertOne($this->dataHeaders);
        }

        foreach ($this->data as $item) {
            $row = [];

            foreach ($this->dataProperties as $property) {
                // Check if property is a boolean (0 or 1 in db)
                if (is_int($item->$property) || is_bool($item->$property)) {
                    $row[] = $item->$property ? 'True' : 'False';
                } elseif ($property == 'notes') {
                    $row[] = $item->$property->all() ? json_encode($item->$property->sortByDesc('updated_at'), JSON_PRETTY_PRINT) : '';
                } else {
                    $row[] = $item->$property;
                }
            }

            $this->writer->insertOne($row);
        };
    }


}
