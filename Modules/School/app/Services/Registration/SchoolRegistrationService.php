<?php

namespace Modules\School\Services\Registration;

use App\Models\Domain;
use App\Support\Databases;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\Company;
use Modules\School\Models\User;
use Modules\School\Services\ActivityLog\ActivityLogService;
use Throwable;

class SchoolRegistrationService
{
    public function __construct(private readonly ActivityLogService $activityLog) {}

    /**
     * Register a school on the host being visited, with its first admin: the company and its
     * domain in the common database, the admin in the school database (Databases::createCompany).
     *
     * sms kept a self-registered school "Pending" until a superadmin approved it; this app has
     * no superadmin, so the school is live as soon as it has its admin.
     *
     * @param  array{school_name: string, phone?: ?string, address?: ?string, admin_name: string, admin_email: string, password: string}  $data
     */
    public function register(string $domain, array $data): User
    {
        try {
            $admin = Databases::createCompany(Databases::SCHOOL, function () use ($domain, $data) {
                $company = Company::create([
                    'name' => $data['school_name'],
                    'slug' => Str::slug($data['school_name']).'-'.Str::lower(Str::random(5)),
                    'email' => $data['admin_email'],
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                    'status' => true,
                ]);

                Domain::create(['company_id' => $company->id, 'domain' => $domain, 'is_primary' => true]);

                return $company;
            }, fn (Company $company) => User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => $data['password'],
                'role' => UserRoleEnum::Admin,
                'company_id' => $company->id,
            ]));

            $this->activityLog->log($admin, 'school.registered', $admin->company, [
                'domain' => $domain,
                'admin_email' => $admin->email,
            ], $admin->company_id);

            return $admin;
        } catch (Throwable $e) {
            Log::error('SchoolRegistrationService::register failed', ['domain' => $domain, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
