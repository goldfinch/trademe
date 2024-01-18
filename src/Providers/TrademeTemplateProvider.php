<?php

namespace Goldfinch\Trademe\Providers;

use Goldfinch\Trademe\Views\Trademe;
use SilverStripe\View\TemplateGlobalProvider;

class TrademeTemplateProvider implements TemplateGlobalProvider
{
    /**
     * @return array|void
     */
    public static function get_template_global_variables(): array
    {
        return ['TrademeService'];
    }

    /**
     * @return boolean
     */
    public static function TrademeService(): Trademe
    {
        return Trademe::create();
    }
}
