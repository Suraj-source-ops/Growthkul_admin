<?php

use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Controllers\Enquiry\EnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Product\CommentController as ProductCommentController;
use App\Http\Controllers\Product\DocumentController as ProductDocumentController;
use App\Http\Controllers\Product\HistoryController as ProductHistoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTrackingController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Team\TeamController;
use App\Http\Controllers\Roles\RolesController;
use App\Http\Controllers\Setting\HomeController as SettingHomeController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {
#Login
Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('validate-user', [LoginController::class, 'validateUser'])->name('validate.user');
Route::get('forget-password', [LoginController::class, 'forgetPassword'])->name('forget.password');
Route::post('send-reset-link', [LoginController::class, 'sendResetLink'])->name('send.reset.link');
Route::get('reset-password/{token}', [LoginController::class, 'resetPassword'])->name('reset.password');
Route::post('password-update', [LoginController::class, 'updatePassword'])->name('password.update');


#Dashboard
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');

    #roles
    Route::match(['get', 'post'], 'roles', [RolesController::class, 'rolesPermissionLists'])->name('roles');
    Route::get('add-role', [RolesController::class, 'addRole'])->name('add.role');
    Route::post('store-role', [RolesController::class, 'storeRoleName'])->name('store.role.name');
    Route::get('roles/{roleId}/change-role-permissions', [RolesController::class, 'changeRolePermissions'])->name('change.role.permissions');
    Route::post('roles/{roleId}/update-roles-permission', [RolesController::class, 'updateRolePermissions'])->name('roles.permissions.update');

    #Teams
    Route::match(['get', 'post'], 'teams', [TeamController::class, 'listTeams'])->name('teams');
    Route::get('add-teams', [TeamController::class, 'addTeams'])->name('add.teams');
    Route::post('store-team', [TeamController::class, 'storeTeam'])->name('team.store');
    Route::get('delete-team/{id}', [TeamController::class, 'deleteTeam'])->name('delete.team');

    #Members
    Route::match(['get', 'post'], 'team-members', [TeamController::class, 'listMembers'])->name('members');
    Route::get('add-team-members', [TeamController::class, 'addMembers'])->name('add.members');
    Route::post('store-team-members', [TeamController::class, 'storeTeamMembers'])->name('team.members.store');
    Route::get('member-status/{id}', [TeamController::class, 'activeOrInactiveMember'])->name('active.inactive');
    Route::get('get-team-members/{teamid}', [TeamController::class, 'getTeamMemberList'])->name('team.member.list');
    Route::get('member/{id}/edit-member-details', [TeamController::class, 'editMemberDetails'])->name('edit.member.details');
    Route::post('member/{id}/update-member-details', [TeamController::class, 'updateMemberDetails'])->name('update.member.details');

    #enquiry
    Route::match(['get', 'post'], 'enquiry', [EnquiryController::class, 'enquiryList'])->name('enquiry');

    #blogs
    Route::match(['get', 'post'], 'blogs', [BlogController::class, 'blogLists'])->name('blogs');
    Route::get('add-blogs', [BlogController::class, 'addBlog'])->name('add.blog');
    Route::get('blog-details/{id}', [BlogController::class, 'blogDetails'])->name('blog.details');
    Route::post('store-blog', [BlogController::class, 'storeBlog'])->name('store.blog');
    Route::get('edit-blogs-details/{id}', [BlogController::class, 'editBlogDetails'])->name('edit.blog.details');
    Route::post('update-blogs-details/{id}', [BlogController::class, 'updateBlogDetails'])->name('update.blog.details');
    Route::get('delete-blog/{id}', [BlogController::class, 'deleteBlog'])->name('delete.blog');
    Route::get('blogs-status/{id}', [BlogController::class, 'activeOrInactiveBlog'])->name('active.inactive.blog');

    #projects
    Route::match(['get', 'post'], 'projects', [ProjectController::class, 'projectLists'])->name('projects');
    Route::get('add-projects', [ProjectController::class, 'addProject'])->name('add.project');
    Route::get('project-details/{id}', [ProjectController::class, 'projectDetails'])->name('project.details');
    Route::post('store-project', [ProjectController::class, 'storeProject'])->name('store.project');
    Route::get('edit-projects-details/{id}', [ProjectController::class, 'editProjectDetails'])->name('edit.project.details');
    Route::post('update-projects-details/{id}', [ProjectController::class, 'updateProjectDetails'])->name('update.project.details');
    Route::get('delete-project/{id}', [ProjectController::class, 'deleteProject'])->name('delete.project');
    Route::get('projects-status/{id}', [ProjectController::class, 'activeOrInactiveProject'])->name('active.inactive.project');

    #setting
    #Services
    Route::match(['get', 'post'], 'service-list', [SettingHomeController::class, 'serviceList'])->name('services.list');
    Route::get('delete-service/{id}', [SettingHomeController::class, 'deleteServiceName'])->name('delete.service.name');
    Route::post('add-services', [SettingHomeController::class, 'addServiceName'])->name('add.service.name');

    #error page permission 
    Route::get('errors', fn() => view('errors.permission-error-page'));
});
//Route::get('product-change', fn() => view('Mail.product-change-notification-template'));
});
