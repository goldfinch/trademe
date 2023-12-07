<?php

namespace Goldfinch\Trademe\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use Goldfinch\Trademe\Models\TrademeItem;
use Goldfinch\Trademe\Configs\TrademeConfig;

class Trademe
{
    /**
     *
     * Trademe API Reference
     * https://developer.trademe.co.nz/api-reference/api-index
     *
     */

    const TRADEME_API_URL = 'https://api.trademe.co.nz/v1/';

    protected $trademe = [];
    protected $cfg;
    protected $client;

    public function __construct()
    {
        $this->configInit();
        $this->clientInit();
    }

    private function clientInit()
    {
        $this->client = new Client();
    }

    private function configInit()
    {
        $this->cfg = TrademeConfig::current_config();

        $this->trademe = [
            // 'consumer_key' => $this->cfg->dbObject('ConsumerKey')->getValue(),
            // 'consumer_secret' => $this->cfg->dbObject('ConsumerSecret')->getValue(),
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }

    // public function TrademeFeed()
    // {
    //     if (!$this->cfg->TrademeAPI)
    //     {
    //         return;
    //     }

    //     if (!$this->trademe['api_key'] || !$this->trademe['limit'] || !$this->trademe['channel_id'])
    //     {
    //         return $this->returnFailed('Missing configuration', 403);
    //     }

    //     try {
    //         $response = $this->client->request('GET', self::TRADEME_API_URL . 'search', [
    //             'query' => [
    //                 'channelId' => $this->trademe['channel_id'],
    //                 'key' => $this->trademe['api_key'],
    //                 'maxResults' => $this->trademe['limit'],
    //                 'part' => 'snippet,id',
    //                 'order' => 'date',
    //             ],
    //             'headers' => $this->trademe['headers'],
    //         ]);
    //     }
    //     catch (ClientException $e) {
    //         $response = $e->getResponse();
    //     }

    //     if ($response->getStatusCode() >= 200  && $response->getStatusCode() < 300)
    //     {
    //         $data = json_decode($response->getBody(), true);

    //         $this->cfg->TrademeAPILastSync = date('Y-m-d H:i:s');
    //         $this->cfg->write();

    //         // $data['pageInfo']
    //         // $data['pageInfo']['totalResults]
    //         // $data['pageInfo']['resultsPerPage]
    //         // $data['nextPageToken']

    //         foreach ($data['items'] as $item)
    //         {
    //             $this->syncPost($item, 'video');
    //         }

    //         return $this->returnSuccess(true);

    //     }
    //     else
    //     {
    //         return $this->returnFailed($response, $response->getStatusCode());
    //     }
    // }

    // private function syncPost($item, $type)
    // {
    //     $video = TrademeItem::get()->filter([
    //       'VideoID' => $item['id']['videoId'],
    //     ])->first();

    //     if ($video)
    //     {
    //         $video->Data = json_encode($item);
    //         $video->write();
    //     }
    //     else
    //     {
    //         if ($type == 'video')
    //         {
    //             $date = Carbon::parse($item['snippet']['publishedAt']);
    //         }

    //         $video = new TrademeItem;
    //         $video->VideoID = $item['id']['videoId'];
    //         $video->PublishDate = $date->format('Y-m-d H:i:s');
    //         $video->Data = json_encode($item);
    //         $video->write();
    //     }
    // }

    private function returnSuccess($data = null, $code = 200)
    {
        print_r([
            'error' => false,
            'status_code' => $code,
            'data' => $data
        ]);
    }

    private function returnFailed($message = null, $code = 500)
    {
        if($message instanceof Response)
        {
            $message = json_decode($message->getBody()->getContents())->error->message;
            $code = 403;
        }
        else if(is_object($message))
        {
            $code = $message->status();
        }

        print_r([
            'error' => true,
            'status_code' => $code,
            'message' => ($message) ? $message['error']['message'] ?? $message : 'Unexpected error occurred'
        ]);
    }
}
