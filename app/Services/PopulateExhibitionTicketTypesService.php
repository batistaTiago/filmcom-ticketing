<?php

namespace App\Services;

use App\Domain\DTO\ExhibitionDTO;
use App\Models\ExhibitionTicketType;
use App\Models\TicketType;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Error;
use Exception;
use Illuminate\Support\Str;

// TODO remove these imports after properly layering this service
use App\Models\ExhibitionSeat;
use App\Models\SeatStatus;
use App\Models\TheaterRoomRow;
use Illuminate\Support\Facades\DB;

class PopulateExhibitionTicketTypesService
{
    public function __construct(private readonly DatabaseManager $databaseManager)
    {
    }

    public function execute(ExhibitionDTO $exhibition, array $ticketTypeUuids)
    {
        try {
            $this->databaseManager->beginTransaction();

            ExhibitionTicketType::query()->insert($this->getInsertData($exhibition, $ticketTypeUuids));

            $this->databaseManager->commit();
        } catch (Exception $e) {
            $this->databaseManager->rollBack();
            throw $e;
        }
    }

    public function getInsertData(ExhibitionDTO $exhibition, array $ticketTypeUuids): array
    {
        if (empty($ticketTypeUuids)) {
            $ticketTypeUuids = TicketType::query()->get()->pluck('uuid')->toArray();
        }

        return array_map(function ($ticketTypeUuid) use ($exhibition, $ticketTypeUuids) {
            return [
                'uuid' => Str::orderedUuid()->toString(),
                'exhibition_id' => $exhibition->uuid,
                'ticket_type_id' => $ticketTypeUuid,
            ];
        }, $ticketTypeUuids);
    }
}
