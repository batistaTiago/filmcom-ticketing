<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Domain\Repositories\ExhibitionRepositoryInterface;
use App\Domain\Repositories\FilmRepositoryInterface;
use App\Domain\Repositories\TheaterRepositoryInterface;
use App\Domain\Repositories\TheaterRoomRepositoryInterface;
use App\Domain\Repositories\TicketTypeRepositoryInterface;
use App\Repositories\MysqlExhibitionRepository;
use App\Repositories\MysqlFilmRepository;
use App\Repositories\MysqlTheaterRepository;
use App\Repositories\MysqlTheaterRoomRepository;
use App\Repositories\MysqlTicketTypeRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(FilmRepositoryInterface::class, MysqlFilmRepository::class);
        $this->app->bind(TheaterRepositoryInterface::class, MysqlTheaterRepository::class);
        $this->app->bind(TheaterRoomRepositoryInterface::class, MysqlTheaterRoomRepository::class);
        $this->app->bind(ExhibitionRepositoryInterface::class, MysqlExhibitionRepository::class);
        $this->app->bind(TicketTypeRepositoryInterface::class, MysqlTicketTypeRepository::class);
    }
}
