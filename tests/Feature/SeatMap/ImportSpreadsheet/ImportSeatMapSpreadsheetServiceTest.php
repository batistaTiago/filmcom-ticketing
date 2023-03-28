<?php

namespace SeatMap\ImportSpreadsheet;

use App\Models\SeatType;
use App\Models\TheaterRoom;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportSeatMapSpreadsheetServiceTest extends TestCase
{
    /** @test */
    public function should_import_an_excel_file()
    {
        SeatType::factory()->create(['name' => SeatType::REGULAR]);
        SeatType::factory()->create(['name' => SeatType::LARGE]);
        SeatType::factory()->create(['name' => SeatType::WHEEL_CHAIR]);

        $room = TheaterRoom::factory()->create();
        Storage::fake('uploads');

        $file = UploadedFile::fake()->createWithContent(
            'test.xlsx',
            file_get_contents('./tests/sample_files/theater_room_seat_map_example_2_sheets.xlsx')
        );

        $response = $this->postJson(route('api.theater-room-seat-map.import'), [
            'file' => $file,
            'theater_room_id' => $room->uuid,
        ]);

        $response->assertOk();

        $this->assertDatabaseCount('theater_room_rows', 8);
        $this->assertDatabaseCount('theater_room_seats', 48);
    }

    /**
     * @test
     * @dataProvider fileUploadProvider
     */
    public function should_validate_the_file_size(UploadedFile $file)
    {
        $room = TheaterRoom::factory()->create();
        Storage::fake('uploads');

        $response = $this->postJson(route('api.theater-room-seat-map.import'), [
            'file' => $file,
            'theater_room_id' => $room->uuid,
        ]);

        $response->assertStatus(400);
    }

    public static function fileUploadProvider()
    {
        $megabyte = 1024;
        return [
            [
                UploadedFile::fake()->create('example.xlsx', 4.1 * $megabyte),
            ],
            [
                UploadedFile::fake()->create('example.xlsx', 5 * $megabyte),
            ],
            [
                UploadedFile::fake()->create('example.xlsx', 10 * $megabyte),
            ],
        ];
    }
}
