<?php

namespace App\Services;

use App\Models\DhcpEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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

    public function exportCsvToFile(string $filePath): void
    {
        $this->writeCsv();
        Storage::put($filePath, $this->writer->toString());
    }

    private function writeCsv(): void
    {
        $this->writer->insertOne($this->dataHeaders);
        $this->data->each(function ($item) {
            $row = [];

            foreach ($this->dataProperties as $property) {
                // Check if property is a boolean (0 or 1 in db)
                if (is_int($item->$property) || is_bool($item->$property)) {
                    $row[] = $item->$property ? 'True' : 'False';
                } elseif ($property == 'notes') {
                    $row[] = $item->$property->all() ? json_encode($item->$property->sortByDesc('updated_at')->first(), JSON_PRETTY_PRINT) : '';
                } else {
                    $row[] = $item->$property;
                }
            }

            $this->writer->insertOne($row);
        });
    }


}
