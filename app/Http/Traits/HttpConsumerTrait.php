<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;

trait HttpConsumerTrait
{
    /**
     * Send a request and return response
     *
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param array $params
     * @return Response
     */
    public function makeRequest(string $method, string $uri, array $headers = [], array $params = [])
    {
        $request = Http::retry(3, 1000);

        if (!empty($headers)) {
            $request = $request->withHeaders($headers);
        }

        $response = $request->{strtolower($method)}($uri, $params);
        $response->throw();

        return $response;
    }
}
