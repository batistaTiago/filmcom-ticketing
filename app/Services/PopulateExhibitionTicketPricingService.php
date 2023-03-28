<?php

namespace App\Services;

use App\Domain\DTO\ExhibitionDTO;
use App\Models\ExhibitionTicketType;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Throwable;

class PopulateExhibitionTicketPricingService
{
    public function __construct(private readonly Connection $databaseConnection)
    {
    }

    public function execute(ExhibitionDTO $exhibition, array $ticketTypes)
    {
        try {
            $this->databaseConnection->beginTransaction();

            ExhibitionTicketType::query()->insert($this->getInsertData($exhibition, $ticketTypes));

            $this->databaseConnection->commit();
        } catch (Throwable $e) {
            $this->databaseConnection->rollBack();
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
                'updated_at' => now(),
            'created_at' => now(),
            ];
        }, $ticketTypes);
    }
}
