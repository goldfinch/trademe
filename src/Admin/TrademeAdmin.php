<?php

namespace Goldfinch\Trademe\Admin;

use Goldfinch\Trademe\Models\TrademeItem;
use Goldfinch\Trademe\Blocks\TrademeBlock;
use Goldfinch\Trademe\Configs\TrademeConfig;
use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;

class TrademeAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'trademe';
    private static $menu_title = 'TradeMe';
    private static $menu_icon_class = 'bi-shop-window';
    // private static $menu_priority = -0.5;

    private static $managed_models = [
        TrademeItem::class => [
            'title'=> 'Questions',
        ],
        TrademeBlock::class => [
            'title'=> 'Blocks',
        ],
        TrademeConfig::class => [
            'title'=> 'Settings',
        ],
    ];

    // public $showImportForm = true;
    // public $showSearchForm = true;
    // private static $page_length = 30;

    public function getList()
    {
        $list = parent::getList();

        // ..

        return $list;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        // ..

        return $config;
    }

    public function getSearchContext()
    {
        $context = parent::getSearchContext();

        // ..

        return $context;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // ..

        return $form;
    }

    // public function getExportFields()
    // {
    //     return [
    //         // 'Name' => 'Name',
    //         // 'Category.Title' => 'Category'
    //     ];
    // }
}
