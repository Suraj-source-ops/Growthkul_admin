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
            'view-service-section-sidebar',
            'view-service-details-sidebar',
            'view-enquiry-section-sidebar',
            'view-project-section-sidebar',
            'view-blogs-section-sidebar',
            'view-role-and-permission-section-sidebar',
            #dashboard permissions
            'view-total-services-dashboard',
            'view-total-projects-dashboard',
            'view-total-blogs-dashboard',
            'view-total-queries-dashboard',
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
            #Services
            'service-lists-services',
            'add-service-button-services',
            'delete-service-button-services',
            #Enquiry
            'enquiry-lists-enquiries',
            #projects
            'project-lists-projects',
            'add-project-button-projects',
            'edit-project-button-projects',
            'update-project-button-projects',
            'delete-project-button-projects',
            'change-status-button-projects',
            #blogs
            'blog-lists-blogs',
            'add-blog-button-blogs',
            'edit-blog-button-blogs',
            'update-blog-button-blogs',
            'delete-blog-button-blogs',
            'change-status-button-blogs',
            #role
            'role-lists-roles',
            'add-role-button-roles',
            'edit-role-button-roles',
            'update-role-button-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
