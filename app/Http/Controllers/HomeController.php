<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Survey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display Surveys
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $lastRun = Cache::tags(['surveys'])->rememberForever('last_run', function () {
            return DB::table('last_run')->where('task', 'surveys_refresh')->first();
        });

        $candidates = Cache::tags(['surveys'])->rememberForever('candidates', function () {
            return Candidate::orderBy('name', 'asc')->get();
        });

        $surveys = Cache::tags(['surveys'])->rememberForever('surveys', function () {
            return Survey::orderBy('identifier', 'desc')->get();
        });

        return view('pages.surveys', compact(
            'candidates',
            'surveys',
            'lastRun'
        ));
    }
}
