<?php

namespace App\Services\Feeds;

use App\Models\User;
use App\Services\Feeds\Providers\NewsAPI;
use App\Services\Feeds\Providers\NewsData;
use App\Services\Feeds\Providers\NewscatcherApi;
use App\Services\Feeds\Providers\Provider;
use Illuminate\Support\Facades\Log;

class Adapter
{

    const NewscatcherApi = 'NewscatcherApi'; # default
    const NewsData = 'NewsData';
    const NewsApi = 'NewsApi';

    private Provider $adapter;

    function __construct(?User $user)
    {

        $providerKey = self::NewscatcherApi;
        if ($user) {
            $providerKey = $user->provider_key;
        }

        switch ($providerKey) {
            case self::NewsApi:
                $this->adapter = new NewsAPI();
                break;

            case self::NewscatcherApi:
                $this->adapter = new NewscatcherApi();
                break;

            case self::NewsData:
                $this->adapter = new NewsData();
                break;

            default:
                throw new \Exception("Invalid news feed provider");
                break;
        }
    }

    function query(string $country, string $category, string $search = ''): string
    {
        Log::info('Adapter Called with: ', [
            'adapter' => $this->adapter->name,
            'payload' => [
                'category' => [
                    'name' => ucwords($category),
                ],
                'country' => [
                    'name' => ucwords($country),
                ],
                'search' => $search
            ]
        ]);

        return json_encode([
            'category' => [
                'name' => ucwords($category),
            ],
            'country' => [
                'name' => ucwords($country),
            ],
            'search' => $search,
            'items' => $this->adapter->query($country,  $category,  $search)
        ]);
    }
}
