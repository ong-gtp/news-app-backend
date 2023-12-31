<?php

namespace App\Services\Feeds\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewscatcherApi extends Provider
{
    public string $name;
    public string $url;
    public string $key;


    function __construct()
    {
        $this->name = 'NewscatcherApi';
        $this->url = 'https://api.newscatcherapi.com';
        $this->key = env('NEWS_CATCHER_API_KEY', '');
    }

    function query(string $country, string $category, string $search = ''): array
    {
        $search = urlencode("news about " . $search);
        $lastWeak = Carbon::now()->subDays(7)->format("Y/m/d");
        $country = strtoupper(self::$supportedCountriesAbbr[$country] ?? $country);
        try {
            $uri = $this->url . "/v2/search?q=$search&from=$lastWeak&countries=$country&page_size=1";
            $response = Http::withHeaders([
                'X-API-KEY' => $this->key
            ])->get($uri);
            $articles = $response->json();
            Log::info(['NewscatcherApi response: ', $uri, $articles]);
            $articles = $articles['articles'];
        } catch (\Exception $e) {
            Log::error([$e->getMessage(), $e->getTraceAsString()]);
            $articles = [];
        }
        return $this->collect($articles);
    }

    public function transform(array $feed): array
    {
        $author = $feed['author'];
        $rights = $feed['rights'];

        return [
            'image' => $feed['media'] ?? '',
            'title' => $feed['title'] ?? '',
            'description' => $feed['summary'] ?? '',
            'author' => strlen($author) > 0 ? $author : $rights,
            'date' => $feed['published_date'] ?? '',
            'link' => $feed['link'] ?? '',
        ];
    }

    public function collect(array $collection): array
    {
        return collect($collection)->map(function ($model) {
            return $this->transform($model);
        })->toArray();
    }
}
