<?php

namespace App\Jobs;

use App\Domain\DTO\ExhibitionDTO;
use App\Services\PopulateExhibitionTicketTypesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateExhibitionTicketTypeAvailabilityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly ExhibitionDTO $exhibition,
        private readonly array $ticketTypeUuids
    ) { }

    public function handle(PopulateExhibitionTicketTypesService $service): void
    {
        $service->execute($this->exhibition, $this->ticketTypeUuids);
    }

    public function tags(): array
    {
        return [
            "process-seat-map-spreadsheet",
            "process-seat-map-spreadsheet:" . Str::orderedUuid()->toString(),
        ];
    }
}
