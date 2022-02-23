<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Survey;
use DB;

class HomeController extends Controller
{
    /**
     * Display Surveys
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $lastRun = DB::table('last_run')->where('task', 'surveys_refresh')->first();
        $candidates = Candidate::orderBy('name', 'asc')->get();
        $surveys = Survey::orderBy('identifier', 'desc')->get();

        return view('pages.surveys', compact(
            'candidates',
            'surveys',
            'lastRun'
        ));
    }
}
