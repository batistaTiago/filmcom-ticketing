<?php

namespace App\UseCases;

use App\Domain\Repositories\TicketTypeRepositoryInterface;
use Illuminate\Support\Collection;

class ListTicketTypesUseCase
{
    public function __construct(private readonly TicketTypeRepositoryInterface $ticketTypeRepository)
    { }

    public function execute(): Collection
    {
        return $this->ticketTypeRepository->getTicketTypes();
    }
}
