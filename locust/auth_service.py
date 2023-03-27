from locust import HttpUser

class AuthService:
    def __init__(self, client: HttpUser):
        self.client = client

    def login(self, email, password):
        credentials = {"email": email, "password": password}
        response = self.client.post("/auth/login", json=credentials, name='Login')

        if response.status_code == 200:
            return response.json()["token"]
        else:
            raise Exception("Failed to authenticate")
