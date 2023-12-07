<?php

namespace Goldfinch\Trademe\Tasks;

use Goldfinch\Trademe\Services\Trademe;
use SilverStripe\Dev\BuildTask;

class TrademeAuthBuildTask extends BuildTask
{
    private static $segment = 'TrademeAuth';

    protected $enabled = true;

    protected $title = 'TradeMe - auth';

    protected $description = 'OAuth TradeMe';

    public function run($request)
    {
        $service = new Trademe;

        $service->TrademeAuth();
    }
}
