<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadSampleSeatMapTest extends TestCase
{
    public function testFileDownload()
    {
        $response = $this->get(route('api.theater-room-seat-map.download-example'));

        $response->assertHeader('Content-Disposition', 'attachment; filename=sample_seat_map.xlsx');
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
