import random
from locust import HttpUser
from auth_service import AuthService

class FilmService:
    def __init__(self, client: HttpUser, auth_service: AuthService):
        self.client = client
        self.auth_service = auth_service

    def get_films(self, token):
        headers = {"Authorization": f"Bearer {token}"}
        next_page_url = "/api/films"
        selected_film = None
        film_count = 0
        page_count = 0

        while next_page_url:
            response = self.client.get(next_page_url, headers=headers, name='Get single film listing page')
            response_data = response.json()

            if response.status_code == 200:
                page_count += 1
                films = response_data["data"]
                for film in films:
                    film_count += 1
                    if random.random() < 0.02:
                        selected_film = film
                        break

                if not selected_film:
                    next_page_url = response_data["next_page_url"]
                else:
                    break
            else:
                raise Exception("Failed to fetch films")

        if not selected_film:
            selected_film = films[-1]

        return selected_film, film_count, page_count
