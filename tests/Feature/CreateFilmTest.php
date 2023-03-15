<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreateFilmTest extends TestCase
{
    /**
     * @test
     * @dataProvider validMovieData
     */
    public function should_be_able_to_create_a_film($filmData)
    {
        $responseJson = $this->postJson(route('api.films.create'), $filmData)
            ->assertCreated()
            ->decodeResponseJson();

        $this->assertDatabaseCount('films', 1);
        $this->assertDatabaseHas('films', [
            'name' => $filmData['name'],
            'year' => $filmData['year'],
            'duration' => $filmData['duration'],
        ]);

        $this->assertArrayHasKey('uuid', $responseJson);
    }

    public static function validMovieData(): array
    {
        return [
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => (string) fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => (string) fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ]
            ],
        ];
    }


    /**
     * @test
     * @dataProvider invalidFilmDataAndExpectations
     */
    public function should_validate_the_input_data($filmData, $keysWithError)
    {
        $responseJson = $this->postJson(route('api.films.create'), $filmData)
            ->assertBadRequest()
            ->decodeResponseJson();

        $this->assertDatabaseCount('films', 0);
        $this->assertArrayHasKey('errors', $responseJson);

        foreach ($keysWithError as $keyWithError) {
            $this->assertArrayHasKey($keyWithError, $responseJson['errors']);
        }
    }

    public static function invalidFilmDataAndExpectations(): array
    {
        return [
            [
                [
                    'name' => '',
                    'year' => fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'name'
                ]
            ],
            [
                [
                    'name' => null,
                    'year' => fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'name'
                ]
            ],
            [
                [
                    'name' => [],
                    'year' => fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'name'
                ]
            ],
            [
                [
                    'year' => fake()->year,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'name'
                ]
            ],





            [
                [
                    'name' => fake()->name,
                    'year' => '',
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => 'abc',
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => null,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => [],
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => -10,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => '-10',
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],

            [
                [
                    'name' => fake()->name,
                    'year' => '-10abc',
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'duration' => fake()->numberBetween(90, 180)
                ],
                [
                    'year'
                ]
            ],





            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => ''
                ],
                [
                    'duration'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => 'abc'
                ],
                [
                    'duration'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => null,
                ],
                [
                    'duration'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => [],
                ],
                [
                    'duration'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => -10,
                ],
                [
                    'duration'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => '-10',
                ],
                [
                    'duration'
                ]
            ],

            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                    'duration' => '-10abc',
                ],
                [
                    'duration'
                ]
            ],
            [
                [
                    'name' => fake()->name,
                    'year' => fake()->year,
                ],
                [
                    'duration'
                ]
            ],
        ];
    }
}
