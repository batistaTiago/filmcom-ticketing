from locust import LoadTestShape

# Import your load tests
from film_creation.film_creation_load_test import FilmCreationLoadTest
from film_picking.film_scrolling_simulation_load_test import FilmScrollingSimulationLoadTest

class FilmLoadTestShape(LoadTestShape):
    # You can customize this class to control the number of users and spawn rate for your tests
    time_limit = 3600
    user_limit = 1000

    def tick(self):
        # You can customize the number of users and spawn rate here
        return 100, 1

# Register both load tests to run simultaneously
FilmCreationLoadTest.user_classes = [FilmCreationLoadTest, FilmScrollingSimulationLoadTest]
