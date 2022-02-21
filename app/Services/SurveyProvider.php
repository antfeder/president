<?php

namespace App\Services;

use App\Http\Traits\HttpConsumerTrait;

class SurveyProvider
{
    use HttpConsumerTrait;

    protected $url;

    public function __construct()
    {
        $this->url = config('app.surveys_source_url');
    }

    /**
     * Fetch data
     *
     * @return array
     */
    public function getData()
    {
        return $this->makeRequest('get', $this->url)->json();
    }
}
