<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\Survey;
use App\Services\SurveyProvider;
use DateTime;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ImportSurveysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surveys:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retreives surveys from source';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SurveyProvider $surveyProvider)
    {
        parent::__construct();
        $this->dataProvider = $surveyProvider;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Retrieving data');
            $data = $this->fetchSurveyData();

            $this->info('Begin transaction');
            DB::beginTransaction();

            $this->mapData($data);

            $this->logLastRun();

            DB::commit();
            $this->info('Transaction commited');
        } catch (Exception $e) {
            $this->error('Something went wrong, Rollback');
            DB::rollBack();

            throw $e;
        }

        $this->flushTagsCache(['surveys']);
        $this->info('Flushin\' Cache Done!');

        return 0;
    }

    /**
     * Retrieve data from source
     *
     * @return array
     */
    private function fetchSurveyData()
    {
        return $this->dataProvider->getData();
    }

    /**
     * Flush all corresponding cache tags entries 
     *
     * @param array $tags
     * @return void
     */
    private function flushTagsCache(array $tags)
    {
        Cache::store('redis')->tags($tags)->flush();
    }

    /**
     * Log the last execution time into database
     *
     * @return void
     */
    private function logLastRun()
    {
        $lastRun = new DateTime();
        DB::table('last_run')->upsert([
            ['task' => 'surveys_refresh', 'created_at' => $lastRun, 'updated_at' => $lastRun],
        ], ['task'], ['updated_at']);
    }

    /**
     * Map data into model(s)
     *
     * @param array $data
     * @return void
     */
    private function mapData(array $data)
    {
        $surveysCollection = Collection::make($data);
        $surveysCollection->each(function ($item, $key) {
            $toursCollection = Collection::make($item['tours']);
            $firstRound = $toursCollection->first(function ($item) {
                return $item['tour'] === "Premier tour";
            });

            $hypothes = $firstRound['hypotheses'][0];
            $candidates = Collection::make($hypothes['candidats']);

            $survey = Survey::create([
                'identifier' => $item['id'],
                'sponsor' => $item['nom_institut'],
                'sample' => $item['echantillon'],
                'start_date' => $item['debut_enquete'],
                'end_date' => $item['fin_enquete'],
            ]);

            $candidates->each(function ($c) use ($survey) {
                $candidate = Candidate::where('name', $c['candidat'])->first();

                if (\is_null($candidate)) {
                    $candidate = Candidate::create([
                        'name' => $c['candidat'],
                        'politic' => $c['parti'][0],
                    ]);
                }

                $survey->candidates()->attach($candidate, ['stat' => $c['intentions']]);
            });
        });
    }
}
