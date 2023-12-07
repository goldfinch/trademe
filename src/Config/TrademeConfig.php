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
        'ConsumerKey' => EncryptedDBText::class,
        'ConsumerSecret' => EncryptedDBText::class,
        'Token' => EncryptedDBText::class,
        'TokenSecret' => EncryptedDBText::class,
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
          'ConsumerKey',
          'ConsumerSecret',
          'Token',
          'TokenSecret',
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
                  TextField::create('ConsumerKey', 'Consumer Key'),
                  TextField::create('ConsumerSecret', 'Consumer Secret'),
                  LiteralField::create('TokenHelp', '<a href="https://developer.trademe.co.nz/api-overview/authentication" target="_blank">Generate an access token</a><br/><br/>'),
                  TextField::create('Token', 'Token'),
                  TextField::create('TokenSecret', 'Token Secret'),

              )->displayIf('TrademeAPI')->isChecked()->end(),

            ),
          ]
        );

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
