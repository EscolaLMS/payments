<?php

namespace EscolaLms\Payments\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentsPermissionsSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::findOrCreate('admin', 'api');

        Permission::findOrCreate('search all payments', 'api');
        Permission::findOrCreate('view payment', 'api');

        $admin->givePermissionTo(['view payment', 'search all payments']);
    }
}
