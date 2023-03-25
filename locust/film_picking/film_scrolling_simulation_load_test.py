from locust import HttpUser, between
from film_picking.tasks.film_picking_task import FilmPickingTask

class FilmScrollingSimulationLoadTest(HttpUser):
    wait_time = between(1, 2)
    tasks = [FilmPickingTask]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
