<?php

namespace Goldfinch\Trademe\Tasks;

use Goldfinch\Trademe\Services\Trademe;
use SilverStripe\Dev\BuildTask;

class TrademeSyncBuildTask extends BuildTask
{
    private static $segment = 'TrademeSync';

    protected $enabled = true;

    protected $title = 'TradeMe - sync';

    protected $description = 'Fetch/sync trademe data';

    public function run($request)
    {
        $service = new Trademe;

        // $service->TrademeFeed();
    }
}
