<?php

namespace App\Support;

/**
 * The app uses one MySQL server with four databases (config/database.php):
 *  - "mysql"      → common: companies, domains, plans, subscriptions, wallet, sessions, cache, jobs
 *  - "restaurant" → Modules/Restaurant tables
 *  - "shop"       → Modules/Shop tables
 *  - "school"     → Modules/School tables
 */
class Databases
{
    public const COMMON = 'mysql';
    public const RESTAURANT = 'restaurant';
    public const SHOP = 'shop';
    public const SCHOOL = 'school';

    /**
     * A common-database table qualified with its database name ("sajilo.companies"),
     * for foreign keys from a module database to a common table, e.g.
     * $table->foreignId('company_id')->constrained(Databases::common('companies')).
     * MySQL allows such cross-database foreign keys while all databases are on one server.
     */
    public static function common(string $table): string
    {
        return config('database.connections.'.self::COMMON.'.database').'.'.$table;
    }

    /**
     * Create a company (common database) and then its module data (module database).
     *
     * The two connections are separate MySQL sessions, so they can't share one transaction:
     * a module row's foreign key to a company that is still uncommitted on the other session
     * would wait on its lock. So the company is committed first, the module data is written in
     * its own transaction, and if that fails the company is deleted again (its foreign keys
     * cascade, removing anything the module wrote).
     *
     * @template T
     * @param  \Closure(): \App\Models\Company  $createCompany  runs in a common-database transaction
     * @param  \Closure(\App\Models\Company): T  $createModuleData  runs in a $moduleConnection transaction
     * @return T
     */
    public static function createCompany(string $moduleConnection, \Closure $createCompany, \Closure $createModuleData): mixed
    {
        $company = \Illuminate\Support\Facades\DB::connection(self::COMMON)->transaction($createCompany);

        try {
            return \Illuminate\Support\Facades\DB::connection($moduleConnection)->transaction(fn () => $createModuleData($company));
        } catch (\Throwable $e) {
            $company->delete();
            throw $e;
        }
    }
}
