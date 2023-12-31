<?php

namespace Goldfinch\Trademe\Tasks;

use Goldfinch\Trademe\Services\Trademe;
use SilverStripe\Dev\BuildTask;

class TrademeSyncBuildTask extends BuildTask
{
    private static $segment = 'TrademeSync';

    protected $enabled = true;

    protected $title = 'TradeMe - fetch/sync data';

    protected $description = 'TradeMe API';

    public function run($request)
    {
        $service = new Trademe;

        $service->TrademeSync();
    }
}
