import random
from faker import Faker
from locust import SequentialTaskSet, task
from auth_service import AuthService

fake = Faker()

class CreateFilmTask(SequentialTaskSet):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.token = None
        self.auth_service = AuthService(self.client)

    def generate_film_data(self):
        name = fake.catch_phrase()
        year = fake.year()
        duration = random.randint(80, 180)

        return {
            "name": name,
            "year": year,
            "duration": duration
        }

    @task
    def login(self):
        email = "admin@filmcom.com"
        password = "password"
        self.token = self.auth_service.login(email, password)

    @task
    def create_film(self):
        if not self.token:
            return

        headers = {"Authorization": f"Bearer {self.token}"}
        film_data = self.generate_film_data()
        response = self.client.post("/api/films", json=film_data, headers=headers, name='Create New Film')

        if response.status_code != 201:
            raise Exception("Failed to create film")
