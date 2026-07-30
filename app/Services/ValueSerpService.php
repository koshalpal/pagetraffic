<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ValueSerpService
{
    public function search(string $query): array
    {
        $apiKey = config('services.valueserp.api_key');
        $baseUrl = rtrim(config('services.valueserp.base_url'), '/');

        if (blank($apiKey)) {
            throw new RuntimeException('ValueSERP API key is not configured.');
        }

        try {
            $response = Http::timeout(60)
                ->acceptJson()
                ->get("{$baseUrl}/search", [
                    'api_key' => $apiKey,
                    'q' => $query,
                    'num' => 10,
                ])
                ->throw();
        } catch (ConnectionException $e) {
            throw new RuntimeException("Could not connect to ValueSERP for query \"{$query}\".", 0, $e);
        } catch (RequestException $e) {
            $message = data_get($e->response?->json(), 'request_info.message')
                ?? data_get($e->response?->json(), 'error')
                ?? $e->getMessage();

            throw new RuntimeException("ValueSERP request failed for query \"{$query}\": {$message}", 0, $e);
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException("ValueSERP returned an invalid response for query \"{$query}\".");
        }

        $status = data_get($payload, 'request_info.success');
        if ($status === false) {
            $message = data_get($payload, 'request_info.message', 'Unknown API error');
            throw new RuntimeException("ValueSERP error for query \"{$query}\": {$message}");
        }

        return $this->extractOrganicResults($payload, $query);
    }

    public function searchMany(array $queries): array
    {
        $aggregated = [];

        foreach ($queries as $query) {
            $aggregated = array_merge($aggregated, $this->search($query));
        }

        return $aggregated;
    }

    protected function extractOrganicResults(array $payload, string $query): array
    {
        $organic = data_get($payload, 'organic_results', []);

        if (! is_array($organic) || $organic === []) {
            return [];
        }

        $results = [];

        foreach ($organic as $item) {
            if (! is_array($item)) {
                continue;
            }

            $results[] = [
                'query' => $query,
                'position' => data_get($item, 'position'),
                'title' => data_get($item, 'title'),
                'link' => data_get($item, 'link'),
                'snippet' => data_get($item, 'snippet'),
                'displayed_link' => data_get($item, 'displayed_link'),
            ];
        }

        return $results;
    }
}
