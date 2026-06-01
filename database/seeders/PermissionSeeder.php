<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Branches
        $riyadhBranch = Branch::firstOrCreate(
            ['code' => 'RIYADH'],
            ['name' => 'فرع الرياض الرئيسي', 'city' => 'الرياض', 'address' => 'طريق خريص الفرعي، الرياض']
        );

        $jeddahBranch = Branch::firstOrCreate(
            ['code' => 'JEDDAH'],
            ['name' => 'فرع جدة الإقليمي', 'city' => 'جدة', 'address' => 'طريق المدينة المنورة، جدة']
        );

        // 2. Create Permissions
        $permissions = [
            'access_dashboard',
            'access_settings',
            'manage_vehicles',
            'view_vehicles',
            'manage_users',
            'manage_appointments',
            'manage_leads',
            'manage_deals',
            'manage_payments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create Roles & Assign Permissions
        
        // Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Branch Manager
        $branchManager = Role::firstOrCreate(['name' => 'branch_manager']);
        $branchManager->givePermissionTo([
            'access_dashboard',
            'manage_vehicles',
            'view_vehicles',
            'manage_leads',
            'manage_deals',
            'manage_payments',
            'manage_appointments',
        ]);

        // Sales Agent
        $salesAgent = Role::firstOrCreate(['name' => 'sales_agent']);
        $salesAgent->givePermissionTo([
            'access_dashboard',
            'view_vehicles',
            'manage_leads',
            'manage_deals',
        ]);

        // Finance Officer
        $financeOfficer = Role::firstOrCreate(['name' => 'finance_officer']);
        $financeOfficer->givePermissionTo([
            'access_dashboard',
            'view_vehicles',
            'manage_deals',
            'manage_payments',
        ]);

        // Reception
        $reception = Role::firstOrCreate(['name' => 'reception']);
        $reception->givePermissionTo([
            'access_dashboard',
            'view_vehicles',
            'manage_appointments',
        ]);

        // 4. Create Seed Users
        
        // Super Admin (No branch scope)
        $userSuper = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'محمود اسعد سعد سعيد',
                'password' => Hash::make('password'),
                'position' => 'إداري سيادي',
                'phone' => '0500000000',
            ]
        );
        $userSuper->assignRole($superAdmin);

        // Riyadh Manager
        $riyadhMgr = User::firstOrCreate(
            ['email' => 'manager_riyadh@gmail.com'],
            [
                'name' => 'أحمد المدير الرياض',
                'password' => Hash::make('password'),
                'position' => 'مدير فرع الرياض',
                'phone' => '0511111111',
                'branch_id' => $riyadhBranch->id,
            ]
        );
        $riyadhMgr->assignRole($branchManager);

        // Riyadh Agent
        $riyadhAgent = User::firstOrCreate(
            ['email' => 'agent_riyadh@gmail.com'],
            [
                'name' => 'فهد مبيعات الرياض',
                'password' => Hash::make('password'),
                'position' => 'مندوب مبيعات الرياض',
                'phone' => '0522222222',
                'branch_id' => $riyadhBranch->id,
            ]
        );
        $riyadhAgent->assignRole($salesAgent);

        // Riyadh Finance
        $riyadhFinance = User::firstOrCreate(
            ['email' => 'finance_riyadh@gmail.com'],
            [
                'name' => 'ياسر مالية الرياض',
                'password' => Hash::make('password'),
                'position' => 'أخصائي مالي الرياض',
                'phone' => '0533333333',
                'branch_id' => $riyadhBranch->id,
            ]
        );
        $riyadhFinance->assignRole($financeOfficer);

        // Riyadh Reception
        $riyadhRecep = User::firstOrCreate(
            ['email' => 'reception_riyadh@gmail.com'],
            [
                'name' => 'سارة استقبال الرياض',
                'password' => Hash::make('password'),
                'position' => 'موظفة استقبال الرياض',
                'phone' => '0544444444',
                'branch_id' => $riyadhBranch->id,
            ]
        );
        $riyadhRecep->assignRole($reception);

        // Jeddah Manager
        $jeddahMgr = User::firstOrCreate(
            ['email' => 'manager_jeddah@gmail.com'],
            [
                'name' => 'سلطان مدير جدة',
                'password' => Hash::make('password'),
                'position' => 'مدير فرع جدة',
                'phone' => '0555555555',
                'branch_id' => $jeddahBranch->id,
            ]
        );
        $jeddahMgr->assignRole($branchManager);
    }
}
