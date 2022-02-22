<?php

namespace Tests\Feature;

use App\Console\Commands\ImportSurveysCommand;
use App\Models\Candidate;
use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;

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
        $file = json_decode(File::get(storage_path('testing/surveys.json')), true);

        Http::fake([
            '*' => Http::response($file),
        ]);

        $command = $this->artisan('surveys:refresh')->run();

        $survey = Survey::withCount('candidates')->first();
        $this->assertEquals(13, $survey->candidates_count);

        $anne = Candidate::with('surveys')->where('name', 'Anne Hidalgo')->first();
        $this->assertEquals(4, $anne->surveys->first()->pivot->stat);

        $jeanLuc = Candidate::where('name', 'Jean-Luc Mélenchon')->first();
        $this->assertEquals('France insoumise', $jeanLuc->politic);

        // from Candidate
        $jeanLuc = Candidate::where('name', 'Jean-Luc Mélenchon')
            ->with(['surveys' => function ($q) {
                $q->where('identifier', '20210223_0224_ifp');
            }])->first();

        $this->assertEquals(12, $jeanLuc->surveys->first()->pivot->stat);

        // from Survey
        $ifpSurvey = Survey::where('identifier', '20210223_0224_ifp')
            ->with(['candidates' => function ($q) {
                $q->where('name', 'Jean-Luc Mélenchon');
            }])->first();

        $this->assertEquals(12, $ifpSurvey->candidates->first()->pivot->stat);
    }
}
