<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Documents\DocumentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Product\CommentController as ProductCommentController;
use App\Http\Controllers\Product\DocumentController as ProductDocumentController;
use App\Http\Controllers\Product\HistoryController as ProductHistoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTrackingController;
use App\Http\Controllers\Team\TeamController;
use App\Http\Controllers\Roles\RolesController;
use App\Http\Controllers\Setting\HomeController as SettingHomeController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;

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

    #client
    Route::match(['get', 'post'], 'clients', [ClientController::class, 'clients'])->name('clients');
    Route::get('add-clients', [ClientController::class, 'addClients'])->name('add.client');
    Route::get('client-details/{id}', [ClientController::class, 'clientsDetails'])->name('client.details');
    Route::post('store-client', [ClientController::class, 'storeClient'])->name('store.client');
    Route::get('edit-clients-details/{id}', [ClientController::class, 'editClientsDetails'])->name('edit.client.details');
    Route::post('update-clients-details/{id}', [ClientController::class, 'updateClientsDetails'])->name('update.client.details');
    Route::get('clients-status/{id}', [ClientController::class, 'activeOrInactiveClient'])->name('active.inactive.client');

    #product
    Route::match(['get', 'post'], 'products', [ProductController::class, 'productLists'])->name('product.lists');
    Route::get('client/{clientid}/add-product', [ProductController::class, 'addProductView'])->name('add.product');
    Route::post('store-product', [ProductController::class, 'storeProduct'])->name('store.product');
    Route::get('product/{slug}/edit-product-details', [ProductController::class, 'editProductDetails'])->name('edit.product.details');
    Route::get('product/{slug}/product-details', [ProductController::class, 'productDetails'])->name('view.product.details');
    Route::post('assign-product', [ProductController::class, 'assignProductToMember'])->name('assign.product.member');
    Route::post('change-product-status', [ProductController::class, 'changeProductStatus'])->name('change.product.status');
    Route::get('delete-product/{productId}', [ProductController::class, 'deleteProduct'])->name('delete.product');
    Route::post('update-due-date', [ProductController::class, 'updateDueDate'])->name('update.product.duedate');
    Route::get('view-file/{docId}', [ProductDocumentController::class, 'viewFile'])->name('view.product.file');
    Route::get('delete-file/{docId}', [ProductDocumentController::class, 'deleteFile'])->name('delete.product.file');
    Route::post('product/{slug}/update-product-details/{type}', [ProductController::class, 'updateProduct'])->name('update.product.details');

    #product comments and history
    Route::post('post-comment', [ProductCommentController::class, 'postComment'])->name('post.comment');
    Route::get('view-comment-file/{docId}', [ProductDocumentController::class, 'viewCommentFile'])->name('view.comment.file');
    #history
    // Route::get('view-history-section', [ProductHistoryController::class, 'viewHistorySection'])->name('view.history.section');
    Route::match(['get', 'post'], 'history', [ProductHistoryController::class, 'productHistory'])->name('product.history.list');
    Route::match(['get', 'post'], 'task-history', [ProductHistoryController::class, 'taskProductHistory'])->name('task.product.history.list');
    #notification
    Route::match(['get', 'post'], '/notification/fetch', [ProductHistoryController::class, 'fetchNotification'])->name('notification.fetch');
    Route::post('/notification/read', [ProductHistoryController::class, 'markAsRead'])->name('notification.read');


    #product tracking
    Route::match(['get', 'post'], 'product/product-tracking', [ProductTrackingController::class, 'productTrackingLists'])->name('product.track.lists');
    Route::match(['get', 'post'], 'product/{productId}/product-tracking', [ProductTrackingController::class, 'productTracking'])->name('product.stages');
    Route::get('change-stage-status/{stageId}', [ProductTrackingController::class, 'changeStatus'])->name('change.stage.status');
    Route::post('update-stage-estimate-date', [ProductTrackingController::class, 'updateStageEstimateDate'])->name('update.stage.estimate.date');
    Route::post('update-stage-notes', [ProductTrackingController::class, 'updateNotes'])->name('update.stage.notes');
    Route::post('stage-file-upload', [ProductTrackingController::class, 'uploadStageFile'])->name('stage.file.upload');

    #setting
    #Product's Graphic Types
    Route::match(['get', 'post'], 'graphic-product-type', [SettingHomeController::class, 'graphicProductTypes'])->name('graphic.product.types');
    Route::get('delete-graphic-type/{id}', [SettingHomeController::class, 'deleteGraphicProductType'])->name('delete.graphic.type');
    Route::post('add-graphic-product', [SettingHomeController::class, 'addGraphicProductType'])->name('add.graphic.product');

    #master stages
    Route::match(['get', 'post'], 'master-stages', [SettingHomeController::class, 'masterStages'])->name('master.stages');
    Route::post('add-stages', [SettingHomeController::class, 'addStages'])->name('add.master.stage');
    Route::get('delete-stages/{id}', [SettingHomeController::class, 'deleteStages'])->name('delete.stages');
    Route::post('change-sequence', [SettingHomeController::class, 'changeSequence'])->name('change.stage.sequence');


    #All Task Section
    Route::group(['prefix' => 'tasks'], function () {
        Route::match(['get', 'post'], '', [TaskController::class, 'allTaskList'])->name('tasks');
        Route::post('assign-task', [TaskController::class, 'assignTaskProductToMember'])->name('assign.task.product.member');
        Route::get('product/{slug}/product-details', [TaskController::class, 'taskProductDetails'])->name('view.tasks.product.details');
        Route::match(['get', 'post'], 'my-tasks', [TaskController::class, 'allTaskList'])->name('mytasks');
    });

    #error page permission spatie
    Route::get('errors', fn() => view('errors.permission-error-page'));
});
Route::get('product-change', fn() => view('Mail.product-change-notification-template'));
