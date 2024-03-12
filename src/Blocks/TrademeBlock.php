<?php

namespace Goldfinch\Trademe\Blocks;

use Goldfinch\Trademe\Models\TrademeItem;
use DNADesign\Elemental\Models\BaseElement;
use Goldfinch\Helpers\Traits\BaseElementTrait;

class TrademeBlock extends BaseElement
{
    use BaseElementTrait;

    private static $table_name = 'TrademeBlock';
    private static $singular_name = 'Trademe';
    private static $plural_name = 'Trademes';

    private static $inline_editable = false;
    private static $description = 'Trademe block';
    private static $icon = 'font-icon-p-shop';

    public function Items($limit)
    {
        return TrademeItem::get()->limit($limit);
    }
}
