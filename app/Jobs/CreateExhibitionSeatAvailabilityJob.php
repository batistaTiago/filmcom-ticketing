<?php

namespace App\Jobs;

use App\Domain\DTO\ExhibitionDTO;
use App\Services\PopulateExhibitionSeatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateExhibitionSeatAvailabilityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly ExhibitionDTO $exhibition)
    { }

    public function handle(PopulateExhibitionSeatsService $service): void
    {
        $service->execute($this->exhibition);
    }

    public function tags(): array
    {
        return [
            "create-exhibition-seat-availability",
            "create-exhibition-seat-availability:" . $this->exhibition->uuid,
        ];
    }
}
