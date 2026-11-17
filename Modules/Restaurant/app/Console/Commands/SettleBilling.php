<?php

namespace Modules\Restaurant\Console\Commands;

use Illuminate\Console\Command;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Support\Billing;

/**
 * Prepaid subscriptions: take the hourly plan charges and pause the ones that ran out of balance.
 * (Also happens on admin/website visits; this keeps idle restaurants up to date.)
 */
class SettleBilling extends Command
{
    protected $signature = 'billing:settle';

    protected $description = 'Charge running subscriptions from company balances';

    public function handle(): int
    {
        $n = 0;
        Company::query()->each(function ($company) use (&$n) {
            Billing::settle($company);
            $n++;
        });

        $this->info("Settled {$n} companies.");

        return self::SUCCESS;
    }
}
