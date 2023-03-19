<?php

namespace App\UseCases;

use App\Jobs\ProcessSeatMapSpreadsheetJob;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ImportSeatMapSpreadsheetUseCase
{
    public function __construct(private readonly Dispatcher $dispatcher)
    {
    }

    public function execute(string $theater_room_id, UploadedFile $file, bool $shouldRebuildMap): void
    {
        $this->dispatcher->dispatch(
            new ProcessSeatMapSpreadsheetJob(
                $theater_room_id,
                Excel::toCollection((object) [], $file),
                $shouldRebuildMap
            )
        );
    }
}

