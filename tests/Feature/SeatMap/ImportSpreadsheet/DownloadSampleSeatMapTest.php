<?php

namespace SeatMap\ImportSpreadsheet;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadSampleSeatMapTest extends TestCase
{
    /** @test */
    public function should_download_the_spreadsheet_in_storage_app_folder()
    {
        Storage::fake();
        $testFile = UploadedFile::fake()->create('sample_seat_map.xlsx');

        $testFile->storeAs('app', 'sample_seat_map.xlsx');

        $response = $this->get(route('api.theater-room-seat-map.download-example'));

        $response->assertHeader('Content-Disposition', 'attachment; filename=sample_seat_map.xlsx');
    }
}
