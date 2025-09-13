<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            #sidebar permissions
            'view-dashboard-sidebar',
            'view-team-details-section-sidebar',
            'view-team-sidebar',
            'view-members-sidebar',
            'view-client-details-sidebar',
            'view-products-details-section-sidebar',
            'view-products-sidebar',
            'view-product-tracking-sidebar',
            'view-tasks-section-sidebar',
            'view-all-tasks-sidebar',
            'view-my-tasks-sidebar',
            'view-role-and-permission-section-sidebar',
            'view-settings-section-sidebar',
            'view-graphic-product-type-sidebar',
            'view-master-product-track-sidebar',
            #dashboard permissions
            'view-total-clients-dashboard',
            'view-total-products-dashboard',
            'view-pending-products-dashboard',
            'view-complete-products-dashboard',
            #Team 
            'team-lists-teams',
            'add-team-button-teams',
            'delete-team-button-teams',
            #Members
            'member-lists-members',
            'add-member-button-members',
            'edit-member-button-members',
            'update-member-button-members',
            'change-status-button-members',
            #Clients
            'client-lists-clients',
            'add-client-button-clients',
            'change-status-button-clients',
            'edit-client-button-clients',
            'update-client-button-clients',
            'delete-client-product-clients',
            'view-client-detail-button-clients',
            'add-client-product-button-clients',
            #products
            'product-lists-products',
            'change-product-due-date-products',
            'assign-product-dropdown-products',
            'change-product-status-dropdown-products',
            'edit-product-button-products',
            'update-product-button-products',
            'view-tracking-products',
            'view-product-comment-button-products',
            #product tasks
            'task-lists-tasks',
            'view-task-comment-button-tasks',
            #role
            'role-lists-roles',
            'add-role-button-roles',
            'edit-role-button-roles',
            'update-role-button-roles',
            #Graphic Products
            // 'graphic-type-lists-graphictypes',
            'add-graphic-types-button-graphictypes',
            'delete-graphic-types-button-graphictypes',
            #Master Stages
            // 'master-stage-lists-masterstage',
            'add-master-stage-button-masterstage',
            'delete-master-stage-button-masterstage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
