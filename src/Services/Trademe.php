<?php

namespace Goldfinch\Trademe\Services;

use Carbon\Carbon;
// use GuzzleHttp\Client;
use JPCaparas\TradeMeAPI\Client;
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

        $OAuthToken = $this->cfg->dbObject($this->envFields['OAuthToken'])->getValue();
        $OAuthTokenSecret = $this->cfg->dbObject($this->envFields['OAuthTokenSecret'])->getValue();
        $OAuthTokenVerifier = $this->cfg->dbObject($this->envFields['OAuthTokenVerifier'])->getValue();

        if ($OAuthToken && $OAuthTokenSecret && $OAuthTokenVerifier)
        {
            $this->trademe['oauth']['token'] = $OAuthToken;
            $this->trademe['oauth']['token_secret'] = $OAuthTokenSecret;
            $this->trademe['oauth']['token_verifier'] = $OAuthTokenVerifier;
        }
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

    public function getSellingItems()
    {
        $client = new Client($this->trademe);

        $params = [];
        $uri = 'MyTradeMe/SellingItems/All.json?photo_size=FullSize';
        $list = $client->api('GET', $uri, $params);

        return json_decode($list, true);
    }

    public function TrademeSync()
    {
        $data = $this->getSellingItems();

        if ($data && $data['TotalCount'] > 0)
        {
            foreach ($data['List'] as $listItem)
            {
                $item = TrademeItem::get()->filter([
                  'ListingID' => $listItem['ListingId'],
                  'Sandbox' => $this->trademe['sandbox'],
                ])->first();

                $startDate = Carbon::createFromTimestamp((int) substr($listItem['StartDate'], 6, -5));
                $endDate = Carbon::createFromTimestamp((int) substr($listItem['EndDate'], 6, -5));

                if (!$item)
                {
                    $item = new TrademeItem;
                    $item->ListingID = $listItem['ListingId'];
                }

                $item->StartDate = $startDate->format('Y-m-d H:i:s');
                $item->EndDate = $endDate->format('Y-m-d H:i:s');
                $item->Data = json_encode($listItem);
                $item->Sandbox = $this->trademe['sandbox'];
                $item->write();
            }
        }
    }

    public function testPlayground()
    {
        // $params = [];
        // $uri = 'Categories.json';
        // dd($client->api('GET', $uri, $params));

        // $params = [
        //     'Category' => '0010-8849-8850-',
        //     'Title' => 'TestTitle',
        //     'Description' => ['TestDescriptionLine1'],
        //     'Duration' => 2,
        //     'BuyNowPrice' => 99,
        //     'StartPrice' => 90,
        //     'PaymentMethods' => [2, 4],
        //     'Pickup' => 1,
        //     'ShippingOptions' => [
        //         ['Type' => 1],
        //     ],
        // ];
        // dd($client->sellItem($params));
    }
}
