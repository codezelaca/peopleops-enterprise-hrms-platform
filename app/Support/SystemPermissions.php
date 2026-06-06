<?php

namespace App\Support;

final class SystemPermissions
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            'admin.dashboard.view',
            'company.profile.manage',
            'users.manage',
            'roles.manage',
            'employees.view',
            'recruitment.view',
            'payroll.view',
            'documents.view',
            'audit.view',
        ];
    }
}
