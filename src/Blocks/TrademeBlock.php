<?php

namespace Goldfinch\Trademe\Blocks;

use Goldfinch\Trademe\Models\TrademeItem;
use DNADesign\Elemental\Models\BaseElement;

class TrademeBlock extends BaseElement
{
    private static $table_name = 'TrademeBlock';
    private static $singular_name = 'Trademe';
    private static $plural_name = 'Trademes';

    private static $db = [];

    private static $inline_editable = false;
    private static $description = '';
    private static $icon = 'font-icon-p-shop';

    public function Items($limit)
    {
        return TrademeItem::get()->limit($limit);
    }

    public function getSummary()
    {
        return $this->getDescription();
    }

    public function getType()
    {
        $default = $this->i18n_singular_name() ?: 'Block';

        return _t(__CLASS__ . '.BlockType', $default);
    }
}
