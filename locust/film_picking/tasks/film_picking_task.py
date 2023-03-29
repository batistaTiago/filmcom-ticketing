from locust import SequentialTaskSet, task
from auth_service import AuthService
from film_picking.tasks.film_service import FilmService

class FilmPickingTask(SequentialTaskSet):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.token = None
        self.auth_service = AuthService(self.client)
        self.film_service = FilmService(self.client, self.auth_service)

    @task
    def login(self):
        email = "admin@filmcom.com"
        password = "password"
        self.token = self.auth_service.login(email, password)

    @task
    def get_films(self):
        if not self.token:
            print("Token was not found...")
            return

        selected_film, film_count, page_count = self.film_service.get_films(self.token)
        print(f"Selected film: Name: {selected_film['name']}, Films seen before choosing: {film_count}, Pages viewed: {page_count}")
