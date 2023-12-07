<?php

namespace Goldfinch\Trademe\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use PhpTek\JSONText\ORM\FieldType\JSONText;

class TrademeItem extends DataObject
{
    private static $table_name = 'TrademeItem';
    private static $singular_name = 'product';
    private static $plural_name = 'products';

    private static $db = [
      'ListingID' => 'Varchar',
      'PublishDate' => 'Datetime',
      'Data' => JSONText::class,
    ];

    private static $summary_fields = [
        'summaryThumbnail' => 'Image',
        'itemTitle' => 'Title',
        'itemDescription' => 'Description',
        'itemDateAgo' => 'Published at',
    ];

    // private static $many_many = [];
    // private static $many_many_extraFields = [];
    // private static $owns = [];
    // private static $has_one = [];
    // private static $belongs_to = [];
    // private static $has_many = [];
    // private static $belongs_many_many = [];
    // private static $default_sort = null;
    // private static $indexes = null;
    // private static $casting = [];
    // private static $defaults = [];
    // private static $field_labels = [];
    // private static $searchable_fields = [];

    // private static $cascade_deletes = [];
    // private static $cascade_duplicates = [];

    // * goldfinch/helpers
    private static $field_descriptions = [];
    private static $required_fields = [];

    public function summaryThumbnail()
    {
        return '-';
    }

    public function itemTitle()
    {
        return '-';
    }

    public function itemDescription()
    {
        return '-';
    }

    public function itemDateAgo()
    {
        return '-';
    }

    public function itemImage()
    {
        //
    }

    public function itemLink()
    {
        $dr = $this->itemData();

        return 'https://www.trademe.co.nz/....';
    }

    public function itemData()
    {
        return new ArrayData($this->dbObject('Data')->getStoreAsArray());
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // ..

        return $fields;
    }

    // public function validate()
    // {
    //     $result = parent::validate();

    //     // $result->addError('Error message');

    //     return $result;
    // }

    // public function onBeforeWrite()
    // {
    //     // ..

    //     parent::onBeforeWrite();
    // }

    // public function onBeforeDelete()
    // {
    //     // ..

    //     parent::onBeforeDelete();
    // }

    // public function canView($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canEdit($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canDelete($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canCreate($member = null, $context = [])
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }
}
