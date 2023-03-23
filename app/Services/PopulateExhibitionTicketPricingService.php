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

class PopulateExhibitionTicketPricingService
{
    public function __construct(private readonly DatabaseManager $databaseManager)
    {
    }

    public function execute(ExhibitionDTO $exhibition, array $ticketTypes)
    {
        try {
            $this->databaseManager->beginTransaction();

            ExhibitionTicketType::query()->insert($this->getInsertData($exhibition, $ticketTypes));

            $this->databaseManager->commit();
        } catch (Exception $e) {
            $this->databaseManager->rollBack();
            throw $e;
        }
    }

    public function getInsertData(ExhibitionDTO $exhibition, array $ticketTypes): array
    {
        return array_map(function ($ticketType) use ($exhibition, $ticketTypes) {
            return [
                'uuid' => Str::orderedUuid()->toString(),
                'exhibition_id' => $exhibition->uuid,
                'ticket_type_id' => $ticketType['uuid'],
                'price' => $ticketType['price'],
            ];
        }, $ticketTypes);
    }
}
