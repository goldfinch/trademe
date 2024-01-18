<?php

namespace Goldfinch\Trademe\Tasks;

use SilverStripe\Dev\BuildTask;
use Goldfinch\Trademe\Services\Trademe;

class TrademeSyncBuildTask extends BuildTask
{
    private static $segment = 'TrademeSync';

    protected $enabled = true;

    protected $title = 'TradeMe - fetch/sync data';

    protected $description = 'TradeMe API';

    public function run($request)
    {
        $service = new Trademe();

        $service->TrademeSync();
    }
}
