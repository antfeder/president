<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Console\Commands\ImportSurveysCommand;
use App\Models\Survey;

class ImportSurveysTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test surveys import artisan command
     *
     * @return void
     */
    public function test_import_surveys_command()
    {
        $command = $this->artisan('surveys:refresh');

        $count = Survey::count();
        $this->assertEquals(1, $count);

    }
}
