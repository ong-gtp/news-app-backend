<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Services\Feeds\FeedService;
use Tymon\JWTAuth\Facades\JWTAuth;
use  \Tests\TestCase;
use  \Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FeedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that feeds api returns feeds with default category when to category is passed
     *
     * @return void
     */
    public function testDefaultFeedCategory()
    {
        $uri = 'api/feeds';
        $response = $this->call(
            'GET',
            $uri,
            [], //params
            [], //cookies
            [], // files
            $this->headers(), // server
            []
        );

        //seed default category
        Category::factory()->create();

        $feedService = new FeedService();
        $defaultCategory = $feedService->defaultCategory();
        $response->assertOk();

        $response->assertJsonStructure([
            "data" => [
                "feeds" => [
                    "category",
                    "items",
                    "country",
                    "search"
                ]
            ]
        ]);
        $resArr = json_decode($response->getContent(), true);
        $this->assertTrue($resArr['data']['feeds']['category']['name'] == ucwords($defaultCategory->name));
    }

    /**
     * Test that feeds api returns feeds with passed category
     *
     * @return void
     */
    public function testFeedCategoryChange()
    {
        //seed default category
        Category::factory()->create();
        // seed sports category
        $category = 'sports';
        Category::factory()->create(['name' => $category]);


        $uri = 'api/feeds?category=' . $category;
        $response = $this->call(
            'GET',
            $uri,
            [], //params
            [], //cookies
            [], // files
            $this->headers(), // server
            []
        );

        $response->assertOk();

        $response->assertJsonStructure([
            "data" => [
                "feeds" => [
                    "category",
                    "items",
                    "country",
                    "search"
                ]
            ]
        ]);
        $resArr = json_decode($response->getContent(), true);
        $this->assertTrue($resArr['data']['feeds']['category']['name'] == ucwords($category));
    }

    /**
     * Test that feeds api returns feeds with default country when to country is passed
     *
     * @return void
     */
    public function testDefaultFeedCountry()
    {
        $uri = 'api/feeds';
        $response = $this->call(
            'GET',
            $uri,
            [], //params
            [], //cookies
            [], // files
            $this->headers(), // server
            []
        );

        //seed default category
        Category::factory()->create();

        $defaultCountry = FeedService::$defaultCountry;
        $response->assertOk();

        $response->assertJsonStructure([
            "data" => [
                "feeds" => [
                    "category",
                    "items",
                    "country",
                    "search",
                ]
            ]
        ]);
        $resArr = json_decode($response->getContent(), true);
        $this->assertTrue($resArr['data']['feeds']['country']['name'] == ucwords($defaultCountry));
    }

    
    /**
     * Test that feeds api returns feeds with passed country
     *
     * @return void
     */
    public function testFeedCountryChange()
    {
        //seed default category
        Category::factory()->create();
        $country = 'nigeria';


        $uri = 'api/feeds?country=' . $country;
        $response = $this->call(
            'GET',
            $uri,
            [], //params
            [], //cookies
            [], // files
            $this->headers(), // server
            []
        );

        $response->assertOk();

        $response->assertJsonStructure([
            "data" => [
                "feeds" => [
                    "category",
                    "items",
                    "country",
                    "search",
                ]
            ]
        ]);
        $resArr = json_decode($response->getContent(), true);
        $this->assertTrue($resArr['data']['feeds']['country']['name'] == ucwords($country));
    }
    
    
    /**
     * Test that feeds api returns feeds with passed search filter
     *
     * @return void
     */
    public function testFeedSearch()
    {
        $search = 'global warming';
        $uri = 'api/feeds?search='.$search ;
        $response = $this->call(
            'GET',
            $uri,
            [], //params
            [], //cookies
            [], // files
            $this->headers(), // server
            []
        );

        $response->assertOk();

        $response->assertJsonStructure([
            "data" => [
                "feeds" => [
                    "category",
                    "items",
                    "country",
                    "search"
                ]
            ]
        ]);
        $resArr = json_decode($response->getContent(), true);
        $this->assertTrue($resArr['data']['feeds']['search'] == $search);
    }
}