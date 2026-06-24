<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Widget;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class WidgetDataController extends Controller
{
    //
    public function getData(Request $request, Widget $widget): JsonResponse
    {
        switch($widget->name)
        {
            case 'city_temperature':
                return $this->getCityTemperature($request);
                
            case 'weather_prediction':
                return $this->getWeatherPrediction($request);
                
            case 'reddit_posts':
                return $this->getRedditPosts($request);

            case 'video_views':
                return $this->getVideoViews($request);

            case'rss_feed':
                return $this->getRssFeed($request);
                
            default:
                return response()->json(['error' => 'Widget data not found'], 404);
        }
    }

    private function getCityTemperature(Request $request): JsonResponse
    {
        $request->validate(['city' => 'required|string']);
        
        $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
            'q' => $request->city,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric',
            'lang' => 'fr'
        ]);
        
        return response()->json([
            'city' => $response['name'],
            'temperature' => $response['main']['temp'],
            'description' => $response['weather'][0]['description'],
            'humidity' => $response['main']['humidity'],

        ]);
    }
    
    private function getWeatherPrediction(Request $request): JsonResponse
    {
        $request->validate(['city' => 'required|string']);
        
        $response = Http::get('https://api.openweathermap.org/data/2.5/forecast', [
            'q' => $request->city,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric',
            'lang' => 'fr'
        ]);
        return response()->json($response->json());
    }

    private function getRedditPosts(Request $request): JsonResponse
    {
        $request->validate([
            'subreddit' => 'required|string',
            'number' => 'required|integer|min:1|max:20'
        ]);
        
        $response = Http::get("https://www.reddit.com/r/{$request->subreddit}/new.json", [
            'limit' => $request->number
        ]);
        
        $posts = collect($response['data']['children'])->map(function ($post) {
            return [
                'title' => $post['data']['title'],
                'url' => $post['data']['url'],
                'author' => $post['data']['author'],
               'score' => $post['data']['score'],
            ];
        });
        
        return response()->json($posts);
    }

    private function getVideoViews(Request $request): JsonResponse
    {
        $request->validate(['video_id' => 'required|string']);
        
        $response = Http::get('https://www.googleapis.com/youtube/v3/videos',[
            'id' => $request->video_id,
            'part' => 'statistics,snippet',
            'key' => env('YOUTUBE_API_KEY')
        ]);
        
       $video = $response['items'][0];
        
        return response()->json([
            'title' => $video['snippet']['title'],
            'views' => $video['statistics']['viewCount'],
            'likes' => $video['statistics']['likeCount'],
        ]);
    }

    private function getRssFeed(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
            'number' => 'required|integer|min:1|max:20'
        ]);
        
        $response = Http::get($request->url);
        $xml = simplexml_load_string($response->body());
        
        $items = collect($xml->channel->item)
                ->take($request->number)
                ->map(function ($item){
                    return [
                        'title' => (string) $item->title,
                        'link' => (string) $item->link,
                        'description' => (string) $item->description,
                        'date' => (string) $item->pubDate
                        
                    ];
                });

        return response()->json($items);
    }
}