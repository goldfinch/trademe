<?php

namespace Goldfinch\Trademe\Models;

use Carbon\Carbon;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\FieldType\DBHTMLText;
use PhpTek\JSONText\ORM\FieldType\JSONText;
use Goldfinch\Trademe\Configs\TrademeConfig;

class TrademeItem extends DataObject
{
    private static $table_name = 'TrademeItem';
    private static $singular_name = 'product';
    private static $plural_name = 'products';

    private static $db = [
        'ListingID' => 'Varchar',
        'StartDate' => 'Datetime',
        'EndDate' => 'Datetime',
        'Data' => JSONText::class,
        'Sandbox' => 'Boolean',
    ];

    private static $summary_fields = [
        'summaryThumbnail' => 'Image',
        'itemTitle' => 'Title',
        // 'itemDescription' => 'Description',
        'itemCategory' => 'Category',
        'itemPrice' => 'Price',
        'summaryStartDate' => 'Start date',
        'summaryEndDate' => 'End date',
    ];

    public function summaryThumbnail()
    {
        $img = $this->itemImage();

        $link =
            '<a onclick="window.open(\'' .
            $this->itemLink() .
            '\');" href="' .
            $this->itemLink() .
            '" target="_blank">';

        if ($img) {
            $img =
                $link .
                '<img class="action-menu__toggle" src="' .
                $img .
                '" alt="Item image" width="250" height="187" style="object-fit: cover" /></a>';
        } else {
            $img = $link . '(no image)</a>';
        }

        $html = DBHTMLText::create();
        $html->setValue($img);

        return $html;
    }

    public function itemTitle()
    {
        $dr = $this->itemData();

        return $dr->Title;
    }

    // public function itemDescription()
    // {
    //     $dr = $this->itemData();

    //     return $dr->Title;
    // }

    public function itemPrice()
    {
        $dr = $this->itemData();

        return $dr->PriceDisplay;
    }

    public function itemCategory()
    {
        $dr = $this->itemData();

        return $dr->CategoryName;
    }

    public function summaryStartDate()
    {
        $html = DBHTMLText::create();
        $str =
            $this->itemStartDate() .
            '<br><small>' .
            $this->itemStartDateAgo() .
            '</small>';
        $html->setValue($str);

        return $html;
    }

    public function summaryEndDate()
    {
        $html = DBHTMLText::create();
        $str =
            $this->itemEndDate() .
            '<br><small>' .
            $this->itemEndDateAgo() .
            '</small>';
        $html->setValue($str);

        return $html;
    }

    public function itemStartDate($format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($this->StartDate)
            ->timezone(date_default_timezone_get())
            ->format($format);
    }

    public function itemEndDate($format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($this->EndDate)
            ->timezone(date_default_timezone_get())
            ->format($format);
    }

    public function itemStartDateAgo()
    {
        return Carbon::parse($this->StartDate)
            ->timezone(date_default_timezone_get())
            ->diffForHumans();
    }

    public function itemEndDateAgo()
    {
        return Carbon::parse($this->EndDate)
            ->timezone(date_default_timezone_get())
            ->diffForHumans();
    }

    public function itemImage()
    {
        $dr = $this->itemData();

        $cfg = TrademeConfig::current_config();

        $return = $dr->PictureHref;

        if ($return && is_array(@getimagesize($return))) {
            return $return;
        } elseif ($cfg->DefaultItemImage()->exists()) {
            return $cfg->DefaultItemImage()->getURL();
        } else {
            return null;
        }
    }

    public function itemLink()
    {
        $dr = $this->itemData();

        if ($this->Sandbox) {
            $link = 'https://www.tmsandbox.co.nz/';
        } else {
            $link = 'https://www.trademe.co.nz/';
        }

        return $link . '/Browse/Listing.aspx?id=' . $this->ListingID;
    }

    public function itemData()
    {
        return new ArrayData($this->dbObject('Data')->getStoreAsArray());
    }
}
