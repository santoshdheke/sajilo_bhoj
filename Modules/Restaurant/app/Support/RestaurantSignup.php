<?php

namespace Modules\Restaurant\Support;

use App\Support\Databases;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Restaurant\Http\Controllers\Admin\SettingController;
use Modules\Restaurant\Models\Branch;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Models\Domain;
use Modules\Restaurant\Models\NavMenuItem;
use Modules\Restaurant\Models\Role;
use Modules\Restaurant\Models\Setting;
use Modules\Restaurant\Models\User;

/**
 * Creates a complete new restaurant workspace: Company + Domain (common database), then
 * the default Branch, website settings, starter menu, Table 1–4, kitchen columns,
 * Owner role + default roles and the owner User (restaurant database).
 *
 * Used by the public sign-up (the owner verifies their email before the workspace goes live)
 * and by Refer & Earn ("Add client restaurant": the referrer vouches for the owner, so the
 * workspace is live at once and the owner can sign in with the details they were given).
 */
class RestaurantSignup
{
    /**
     * @param  array{company_name: string, owner_name: string, owner_email: string, password: string, theme_color?: ?string}  $data
     * @param  Company|null  $referrer  set for a client added through Refer & Earn (owner counts as verified)
     * @return User the owner
     */
    public static function create(array $data, string $domain, ?Company $referrer = null): User
    {
        return Databases::createCompany(Databases::RESTAURANT, function () use ($data, $domain, $referrer) {
            $company = Company::create([
                'name'                   => $data['company_name'],
                'slug'                   => Str::slug($data['company_name']).'-'.Str::lower(Str::random(5)),
                'email'                  => $data['owner_email'],
                'status'                 => true,
                'referred_by_company_id' => $referrer?->id,
            ]);

            Domain::create([
                'company_id' => $company->id,
                'domain'     => $domain,
                'is_primary' => true,
            ]);

            return $company;
        }, function (Company $company) use ($data, $referrer) {
            $branch = Branch::create([
                'company_id' => $company->id,
                'name'       => 'Main Branch',
                'email'      => $data['owner_email'],
                'status'     => true,
            ]);

            self::seedSettings($company, $data);
            DefaultMenu::seed($branch);
            DefaultTables::seed($branch);
            DefaultKitchen::seed($branch);

            $role = Role::create([
                'company_id'  => $company->id,
                'name'        => 'Owner',
                'description' => 'Full access to every module.',
                'permissions' => ['*'],
            ]);
            DefaultRoles::seed($company);

            $owner = User::create([
                'company_id' => $company->id,
                'branch_id'  => $branch->id,
                'role_id'    => $role->id,
                'name'       => $data['owner_name'],
                'email'      => $data['owner_email'],
                'password'   => Hash::make($data['password']),
                'is_active'  => true,
            ]);

            if ($referrer) {
                $owner->forceFill(['email_verified_at' => now()])->save();
            }

            return $owner;
        });
    }

    /**
     * Give a new company a complete set of website settings (every settings section),
     * personalised with its name, email and chosen brand colour, plus the default
     * navigation menu — so the storefront looks finished from the first visit.
     */
    private static function seedSettings(Company $company, array $data): void
    {
        $name = $data['company_name'];

        $values = array_merge(SettingController::allDefaults(), [
            'restaurant_name' => $name,
            'tagline'         => 'Fresh taste, warm service, elegant dining.',
            'theme_color'     => $data['theme_color'] ?? '#198754',
            'contact_email'   => $data['owner_email'],
            'opening_hours'   => "Sunday - Friday: 10:00 AM - 10:00 PM\nSaturday: 11:00 AM - 11:00 PM",
            'about_text'      => "{$name} brings people together over honest, freshly prepared food. "
                               . 'From our kitchen to your table, every dish is cooked with care, served with a smile '
                               . 'and priced to keep you coming back.',
        ]);

        foreach ($values as $key => $value) {
            Setting::putValue($company->id, $key, $value);
        }

        NavMenuItem::forCompany($company->id);
    }
}
