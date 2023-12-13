<?php

namespace Goldfinch\Trademe\Admin;

use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Trademe\Models\TrademeItem;
use Goldfinch\Trademe\Blocks\TrademeBlock;
use Goldfinch\Trademe\Configs\TrademeConfig;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;

class TrademeAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'trademe';
    private static $menu_title = 'Trade Me';
    private static $menu_icon_class = 'font-icon-p-shop';
    // private static $menu_priority = -0.5;

    private static $managed_models = [
        TrademeItem::class => [
            'title'=> 'Products',
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

        $cfg = TrademeConfig::current_config();
        $list = $list->filter('Sandbox', $cfg->Sandbox);

        return $list;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        if ($this->modelClass == TrademeItem::class)
        {
            $config->removeComponentsByType(GridFieldAddNewButton::class);
            $config->removeComponentsByType(GridFieldEditButton::class);
        }

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
