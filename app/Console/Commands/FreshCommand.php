<?php

namespace App\Console\Commands;

use App\Support\Databases;
use Illuminate\Database\Console\Migrations\FreshCommand as BaseFreshCommand;
use Illuminate\Support\Facades\Schema;

/**
 * `migrate:fresh` for the three-database setup (config/database.php).
 *
 * Laravel's version only wipes the default ("mysql") database, then re-runs every migration:
 * the module migrations (connection "restaurant" / "shop" / "school") then fail on tables that still
 * exist, leaving the common database half built. Without --database, this also drops the
 * module databases' tables first. Registered in App\Providers\AppServiceProvider.
 */
class FreshCommand extends BaseFreshCommand
{
    /** Module connections wiped together with the default one. */
    private const MODULE_CONNECTIONS = [Databases::RESTAURANT, Databases::SHOP, Databases::SCHOOL];

    public function handle()
    {
        if ($this->isProhibited() || ! $this->confirmToProceed()) {
            return self::FAILURE;
        }

        if (! $this->input->getOption('database')) {
            foreach (self::MODULE_CONNECTIONS as $connection) {
                $this->components->task("Dropping all tables ({$connection})", function () use ($connection) {
                    Schema::connection($connection)->dropAllTables();

                    return true;
                });
            }
        }

        // Already confirmed above: don't ask again in production.
        $this->input->setOption('force', true);

        return parent::handle();
    }
}
