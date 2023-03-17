<?php

namespace App\UseCases;

use App\Domain\Repositories\TicketTypeRepositoryInterface;

class ListExhibitionTicketTypesUseCase
{
    public function __construct(private readonly TicketTypeRepositoryInterface $ticketTypeRepository)
    { }

    public function execute(string $exhibition_id)
    {
        return $this->ticketTypeRepository->getExhibitionTicketTypes($exhibition_id);
    }
}
