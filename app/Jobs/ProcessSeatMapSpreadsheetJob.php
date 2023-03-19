<?php

namespace App\Jobs;

use App\Domain\DTO\ExhibitionDTO;
use App\Services\PopulateExhibitionSeatsService;
use App\Services\PopulateSeatMapService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProcessSeatMapSpreadsheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly string $theater_room_id,
        private readonly Collection $sheets,
        private readonly bool $shouldRebuildMap
    ) {
    }

    public function handle(PopulateSeatMapService $service): void
    {
        $service->execute(
            $this->theater_room_id,
            $this->sheets,
            $this->shouldRebuildMap
        );
    }

    public function tags(): array
    {
        return [
            "create-exhibition-seat-availability",
            "create-exhibition-seat-availability:" . Str::orderedUuid(),
        ];
    }
}
