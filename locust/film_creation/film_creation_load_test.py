from locust import HttpUser, between
from film_creation.tasks.create_film_task import CreateFilmTask

class FilmCreationLoadTest(HttpUser):
    wait_time = between(1, 2)
    tasks = [CreateFilmTask]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
