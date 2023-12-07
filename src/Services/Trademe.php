<?php

namespace Goldfinch\Trademe\Services;

use Carbon\Carbon;
// use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use Goldfinch\Trademe\Models\TrademeItem;
use Goldfinch\Trademe\Configs\TrademeConfig;
use JPCaparas\TradeMeAPI\Client;

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
    protected $envFields = [];
    protected $cfg;
    protected $client;

    public function __construct()
    {
        $this->configInit();
        $this->clientInit();
    }

    private function clientInit()
    {
        $this->client = new Client($this->trademe);
    }

    private function configInit()
    {
        $this->cfg = TrademeConfig::current_config();

        $sandbox = $this->cfg->dbObject('Sandbox')->getValue() ? true : false;

        $this->envFields = [
            'ConsumerKey' => $sandbox ? 'SandboxConsumerKey' : 'ConsumerKey',
            'ConsumerSecret' => $sandbox ? 'SandboxConsumerSecret' : 'ConsumerSecret',
            'OAuthToken' => $sandbox ? 'SandboxOAuthToken' : 'OAuthToken',
            'OAuthTokenSecret' => $sandbox ? 'SandboxOAuthTokenSecret' : 'OAuthTokenSecret',
            'OAuthTokenVerifier' => $sandbox ? 'SandboxOAuthTokenVerifier' : 'OAuthTokenVerifier',
            'VerifierURL' => $sandbox ? 'SandboxVerifierURL' : 'VerifierURL',
        ];

        $this->trademe = [
            'sandbox' => $sandbox,
            'oauth' => [
                'consumer_key' => $this->cfg->dbObject($this->envFields['ConsumerKey'])->getValue(),
                'consumer_secret' => $this->cfg->dbObject($this->envFields['ConsumerSecret'])->getValue(),
            ],
        ];
    }

    public function TrademeAuth()
    {
        if (
          !isset($_GET['oauth_token']) ||
          !isset($_GET['oauth_verifier']) ||
          $this->cfg->dbObject($this->envFields['OAuthToken'])->getValue() != $_GET['oauth_token']
        )
        {
            // 1)
            $data = $this->client->getTemporaryAccessTokens();

            $oauth_token = $data['oauth_token'];
            $oauth_token_secret = $data['oauth_token_secret'];

            if (isset($data['oauth_callback_confirmed']) && $data['oauth_callback_confirmed'])
            {
                $this->cfg->{$this->envFields['OAuthToken']} = $oauth_token;
                $this->cfg->{$this->envFields['OAuthTokenSecret']} = $oauth_token_secret;
            }
            else
            {
                echo 'Callback not confirmed';
                exit;
            }

            // 2) Visit this URL and store the verifier code
            $tokenVerifierUrl = $this->client->getAccessTokenVerifierURL($oauth_token);
            $this->cfg->{$this->envFields['OAuthTokenVerifier']} = '';
            $this->cfg->{$this->envFields['VerifierURL']} = $tokenVerifierUrl;

            $this->cfg->write();

            echo '<a href="'.$tokenVerifierUrl.'">Click to verify</a>';
            exit;
        }
        else
        {
            // 3)
            $config = [
              'temp_token' => $this->cfg->dbObject($this->envFields['OAuthToken'])->getValue(),
              'temp_token_secret' => $this->cfg->dbObject($this->envFields['OAuthTokenSecret'])->getValue(),
              'token_verifier' => $_GET['oauth_verifier']
            ];

            $final = $this->client->getFinalAccessTokens($config);

            if (isset($final['oauth_token']) && isset($final['oauth_token_secret']))
            {
                $this->cfg->{$this->envFields['OAuthToken']} = $final['oauth_token'];
                $this->cfg->{$this->envFields['OAuthTokenSecret']} = $final['oauth_token_secret'];
                $this->cfg->{$this->envFields['OAuthTokenVerifier']} = $_GET['oauth_verifier'];
                $this->cfg->{$this->envFields['VerifierURL']} = '';
                $this->cfg->write();

                echo 'All done';
                exit;
            }
        }
    }

    public function TrademeSync()
    {
        $client = new Client($this->trademe);
        $params = [];
        $uri = 'Categories.json';

        dd($client->api('GET', $uri, $params));
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
