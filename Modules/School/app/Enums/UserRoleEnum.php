<?php

namespace Modules\School\Enums;

enum UserRoleEnum: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Teacher => 'Teacher',
            self::Admin => 'Admin',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Route name of the landing page a user of this role is sent to after login.
     */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Student => 'school.student.dashboard',
            self::Teacher => 'school.teacher.dashboard',
            self::Admin => 'school.admin.dashboard',
        };
    }
}
