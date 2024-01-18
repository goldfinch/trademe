<?php

namespace Goldfinch\Trademe\Tasks;

use Goldfinch\Trademe\Services\Trademe;
use SilverStripe\CronTask\Interfaces\CronTask;

class TrademeAuthCronTask implements CronTask
{
    /**
     * run this task every 60 minutes
     *
     * @return string
     */
    public function getSchedule()
    {
        return '*/60 * * * *';
    }

    /**
     *
     * @return void
     */
    public function process()
    {
        $service = new Trademe();

        $service->TrademeAuth();
    }
}
