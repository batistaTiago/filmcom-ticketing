import random
from locust import HttpUser
from auth_service import AuthService

class FilmService:
    def __init__(self, client: HttpUser, auth_service: AuthService):
        self.client = client
        self.auth_service = auth_service

    def get_film_page(self, url, token):
        headers = {"Authorization": f"Bearer {token}"}
        response = self.client.get(url, headers=headers, name='Get single film listing page')
        return response

    def process_film_page(self, films):
        selected_film = None
        film_count = 0

        for film in films:
            film_count += 1
            if random.random() < 0.02:
                selected_film = film
                break

        return selected_film, film_count

    def get_next_page_url(self, response_data):
        return response_data["next_page_url"]

    def get_selected_film(self, token):
        next_page_url = "/api/films"
        selected_film = None
        film_count = 0
        page_count = 0

        while next_page_url:
            response = self.get_film_page(next_page_url, token)
            response_data = response.json()

            if response.status_code == 200:
                page_count += 1
                films = response_data["data"]

                selected_film, new_film_count = self.process_film_page(films)
                film_count += new_film_count

                if not selected_film:
                    next_page_url = self.get_next_page_url(response_data)
                else:
                    break
            else:
                raise Exception("Failed to fetch films")

        if not selected_film:
            selected_film = films[-1]

        return selected_film, film_count, page_count
