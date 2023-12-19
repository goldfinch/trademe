<?php

namespace Goldfinch\Trademe\Configs;

use SilverStripe\Assets\Image;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use LeKoala\Encrypt\EncryptHelper;
use LeKoala\Encrypt\EncryptedDBText;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\AssetAdmin\Forms\UploadField;

class TrademeConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'TrademeConfig';

    private static $db = [
        'TrademeAPI' => 'Boolean',
        'Sandbox' => 'Boolean',

        'SandboxConsumerKey' => EncryptedDBText::class,
        'SandboxConsumerSecret' => EncryptedDBText::class,
        'SandboxOAuthToken' => EncryptedDBText::class,
        'SandboxOAuthTokenSecret' => EncryptedDBText::class,
        'SandboxOAuthTokenVerifier' => EncryptedDBText::class,
        'SandboxVerifierURL' => EncryptedDBText::class,

        'ConsumerKey' => EncryptedDBText::class,
        'ConsumerSecret' => EncryptedDBText::class,
        'OAuthToken' => EncryptedDBText::class,
        'OAuthTokenSecret' => EncryptedDBText::class,
        'OAuthTokenVerifier' => EncryptedDBText::class,
        'VerifierURL' => EncryptedDBText::class,
    ];

    private static $has_one = [
      'DefaultItemImage' => Image::class,
  ];

  private static $owns = [
      'DefaultItemImage',
  ];

    private static $field_descriptions = [];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
          'TrademeAPI',
          'Sandbox',

          'SandboxConsumerKey',
          'SandboxConsumerSecret',
          'SandboxOAuthToken',
          'SandboxOAuthTokenSecret',
          'SandboxOAuthTokenVerifier',
          'SandboxVerifierURL',

          'ConsumerKey',
          'ConsumerSecret',
          'OAuthToken',
          'OAuthTokenSecret',
          'OAuthTokenVerifier',
          'VerifierURL',
      ]);

      $fields->addFieldsToTab(
          'Root.Main',
          [
            UploadField::create(
              'DefaultItemImage',
              'Default item image',
            )->setDescription('for items that do not have an image, or by some reason return nothing'),

            CompositeField::create(

              CheckboxField::create('TrademeAPI', 'TradeMe API'),
              Wrapper::create(

                  LiteralField::create('ConsumerKeyHelp', '<a href="https://developer.trademe.co.nz/api-overview/registering-an-application" target="_blank">Registering an Application</a><br/><br/>'),
                  CheckboxField::create('Sandbox', 'Sandbox'),
                  LiteralField::create('OAuthTokenHelp', '<br><a href="https://developer.trademe.co.nz/api-overview/authentication" target="_blank">Generate an access token</a><br/><br/>'),

                  Wrapper::create(

                      TextField::create('SandboxConsumerKey', 'Consumer Key'),
                      TextField::create('SandboxConsumerSecret', 'Consumer Secret'),
                      TextField::create('SandboxOAuthToken', 'OAuth Token'),
                      TextField::create('SandboxOAuthTokenSecret', 'OAuth Token Secret'),
                      TextField::create('SandboxOAuthTokenVerifier', 'OAuth Token Verifier'),
                      LiteralField::create('SandboxVerifierURLHelp', $this->SandboxVerifierURL ? '<a href="' . $this->dbObject('SandboxVerifierURL')->getValue() . '" target="_blank">Get Verifier Token</a><br/><br/>' : ''),

                  )->displayIf('Sandbox')->isChecked()->end(),

                  Wrapper::create(

                      TextField::create('ConsumerKey', 'Consumer Key'),
                      TextField::create('ConsumerSecret', 'Consumer Secret'),
                      TextField::create('OAuthToken', 'OAuth Token'),
                      TextField::create('OAuthTokenSecret', 'OAuth Token Secret'),
                      TextField::create('OAuthTokenVerifier', 'OAuth Token Verifier'),
                      LiteralField::create('VerifierURLHelp', $this->VerifierURL ? '<a href="' . $this->dbObject('SandboxVerifierURL')->getValue() . '" target="_blank">Get Verifier Token</a><br/><br/>' : ''),

                  )->displayIf('Sandbox')->isNotChecked()->end(),

              )->displayIf('TrademeAPI')->isChecked()->end(),

            ),
          ]
        );

        $fields->dataFieldByName('DefaultItemImage')->setFolderName('trademe');

        // Set Encrypted Data
        $this->nestEncryptedData($fields);

        return $fields;
    }

    protected function nestEncryptedData(FieldList &$fields)
    {
        foreach($this::$db as $name => $type)
        {
            if (EncryptHelper::isEncryptedField(get_class($this->owner), $name))
            {
                $this->$name = $this->dbObject($name)->getValue();
            }
        }
    }
}
