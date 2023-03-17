<?php

namespace App\Jobs;

use App\Domain\DTO\ExhibitionDTO;
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Models\TheaterRoomRow;
use Error;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateExhibitionSeatAvailabilityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private readonly ExhibitionDTO $exhibition)
    { }

    public function handle(): void
    {
        try {
            // TODO layerize this code
            DB::beginTransaction();
            $rows = TheaterRoomRow::with('seats')->where('theater_room_id', $this->exhibition->theater_room_id)->get();
            $defaultSeatStatus = SeatStatus::where(['name' => SeatStatus::AVAILABLE])->first();

            if (empty($defaultSeatStatus)) {
                throw new Error('The default seat status has not been setup.');
            }

            ExhibitionSeat::query()->insert($this->getInsertData($rows, $defaultSeatStatus));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function tags(): array
    {
        return [
            "create-exhibition-seat-availability",
            "create-exhibition-seat-availability:{$this->exhibition->uuid}",
        ];
    }

    private function getInsertData(Collection $rows, SeatStatus $defaultSeatStatus): array {
        $insertData = [];

        foreach ($rows as $row) {
            foreach ($row->seats as $seat) {
                $insertData[] = [
                    'uuid' => Str::orderedUuid()->toString(),
                    'exhibition_id' => $this->exhibition->uuid,
                    'theater_room_seat_id' => $seat->uuid,
                    'seat_status_id' => $defaultSeatStatus->uuid,
                ];
            }
        }

        return $insertData;
    }
}
