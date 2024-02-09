<?php

namespace Goldfinch\Trademe\Blocks;

use Goldfinch\Trademe\Models\TrademeItem;
use DNADesign\Elemental\Models\BaseElement;

class TrademeBlock extends BaseElement
{
    private static $table_name = 'TrademeBlock';
    private static $singular_name = 'Trademe';
    private static $plural_name = 'Trademes';

    private static $inline_editable = false;
    private static $icon = 'font-icon-p-shop';

    public function Items($limit)
    {
        return TrademeItem::get()->limit($limit);
    }
}
