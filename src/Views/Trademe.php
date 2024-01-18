<?php

namespace Goldfinch\Trademe\Views;

use SilverStripe\View\ViewableData;
use Goldfinch\Trademe\Models\TrademeItem;
use Goldfinch\Trademe\Configs\TrademeConfig;

class Trademe extends ViewableData
{
    public function TrademeItems($limit = null)
    {
        if (!$this->authorized('TrademeAPI'))
        {
            return;
        }

        if ($limit === null || $limit === '')
        {
            $cfg = $this->getCfg();
            $limit = $cfg->dbObject('TrademeLimit')->getValue() ?? 10;
        }

        return TrademeItem::get()->limit($limit);
    }

    public function TrademeFeed($limit = null)
    {
        if (!$this->authorized('TrademeAPI'))
        {
            return;
        }

        $cfg = $this->getCfg();

        if ($limit === null || $limit === '')
        {
            $limit = $cfg->dbObject('TrademeLimit')->getValue() ?? 10;
        }

        return $this->customise(['cfg' => $cfg, 'limit' => $limit])->renderWith('Views/TrademeFeed');
    }

    public function forTemplate()
    {
        if (!$this->authorized('TrademeAPI'))
        {
            return;
        }

        return $this->renderWith('Views/TrademeFeed');
    }

    private function authorized($state)
    {
        $cfg = TrademeConfig::current_config();

        if ($cfg->$state)
        {
            return true;
        }

        return false;
    }

    private function getCfg()
    {
        return TrademeConfig::current_config();
    }
}
