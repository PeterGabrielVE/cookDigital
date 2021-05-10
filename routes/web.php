<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/register', function () {
    return redirect('/login');
});

Route::get('/login/{lang?}', 'Auth\LoginController@showLoginForm')->name('login');
Route::get('/reset/password/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('password.request');

Route::get('searchJson', 'ProjectsController@getSearchJson')->name('search.json')->middleware(['auth','XSS']);

Route::get('/', 'DashboardController@index')->name('dashboard')->middleware(['XSS',]);
Route::get('/dashboard', 'DashboardController@index')->name('dashboard')->middleware(['auth','XSS',]);
Route::get('profile', 'UserController@profile')->name('profile')->middleware(['auth','XSS',]);
Route::post('edit-profile', 'UserController@editprofile')->name('update.account')->middleware(['auth','XSS',]);

Route::resource('users', 'UserController')->middleware(['auth','XSS',]);
Route::post('change-password', 'UserController@updatePassword')->name('update.password');

Route::resource('clients', 'ClientController')->middleware(['auth','XSS',]);

Route::resource('roles', 'RoleController')->middleware(['auth','XSS',]);
Route::resource('permissions', 'PermissionController')->middleware(['auth','XSS',]);

Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::get('change-language/{lang}', 'LanguageController@changeLanquage')->name('change.language');
    Route::get('manage-language/{lang}', 'LanguageController@manageLanguage')->name('manage.language');
    Route::post('store-language-data/{lang}', 'LanguageController@storeLanguageData')->name('store.language.data');
    Route::get('create-language', 'LanguageController@createLanguage')->name('create.language');
    Route::post('store-language', 'LanguageController@storeLanguage')->name('store.language');
    Route::delete('destroy-language/{lang}','LanguageController@destroyLang')->name('destroy.language');
});

Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::resource('systems', 'SystemController');
    Route::post('email-settings', 'SystemController@saveEmailSettings')->name('email.settings');
    Route::post('company-settings', 'SystemController@saveCompanySettings')->name('company.settings');
    Route::post('company-payment-settings', 'SystemController@saveCompanyPaymentSettings')->name('company.payment.settings');
    Route::post('stripe-settings', 'SystemController@saveStripeSettings')->name('stripe.settings');
    Route::post('system-settings', 'SystemController@saveSystemSettings')->name('system.settings');
    Route::post('pusher-settings', 'SystemController@savePusherSettings')->name('pusher.settings');
    Route::get('company-setting', 'SystemController@companyIndex')->name('company.setting');
    Route::post('/template-setting',['as' => 'template.setting','uses' =>'SystemController@saveTemplateSettings']);
    Route::get('/test',['as' => 'test.email','uses' =>'SystemController@testEmail']);
    Route::post('/test/send',['as' => 'test.email.send','uses' =>'SystemController@testEmailSend']);
});

Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::resource('leadstages', 'LeadstagesController');
    Route::post('/leadstages/order', ['as' => 'leadstages.order','uses' => 'LeadstagesController@order',]);
});

Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::resource('projectstages', 'ProjectstagesController');
    Route::post('/projectstages/order', ['as' => 'projectstages.order','uses' => 'ProjectstagesController@order',]);
});

Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::resource('leadsources', 'LeadsourceController');
});

Route::resource('labels', 'LabelsController')->middleware(['auth','XSS',]);
Route::resource('productunits', 'ProductunitsController')->middleware(['auth','XSS',]);
Route::resource('expensescategory', 'ExpensesCategoryController')->middleware(['auth','XSS',]);
Route::post('/leads/order', ['as' => 'leads.order','uses' => 'LeadsController@order',])->middleware(['auth','XSS',]);
Route::resource('leads', 'LeadsController')->middleware(['auth','XSS',]);


Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::post('projects/{id}/status', 'ProjectsController@updateStatus')->name('projects.update.status');
    Route::resource('projects', 'ProjectsController');
    Route::get('project-invite/{project_id}', 'ProjectsController@userInvite')->name('project.invite');
    Route::post('invite/{project}', 'ProjectsController@Invite')->name('invite');
    Route::delete('project/{project_id}/user/{id}', 'ProjectsController@removeUser')->name('project.remove.user');

    Route::get('projects/{id}/milestone', 'ProjectsController@milestone')->name('project.milestone');
    Route::post('projects/{id}/milestone', 'ProjectsController@milestoneStore')->name('project.milestone.store');
    Route::get('projects/milestone/{id}/edit', 'ProjectsController@milestoneEdit')->name('project.milestone.edit');
    Route::post('projects/milestone/{id}/update', 'ProjectsController@milestoneUpdate')->name('project.milestone.update');
    Route::delete('projects/milestone/{id}', 'ProjectsController@milestoneDestroy')->name('project.milestone.destroy');
    Route::get('projects/milestone/{id}/show', 'ProjectsController@milestoneShow')->name('project.milestone.show');

    Route::post('projects/{id}/file', 'ProjectsController@fileUpload')->name('project.file.upload');
    Route::get('projects/{id}/file/{fid}', 'ProjectsController@fileDownload')->name('projects.file.download');
    Route::delete('projects/{id}/file/delete/{fid}', 'ProjectsController@fileDelete')->name('projects.file.delete');

    Route::get('projects/{id}/taskboard', 'ProjectsController@taskBoard')->name('project.taskboard');
    Route::get('projects/{id}/taskboard/create', 'ProjectsController@taskCreate')->name('task.create');
    Route::post('projects/{id}/taskboard/store', 'ProjectsController@taskStore')->name('task.store');
    Route::get('projects/taskboard/{id}/edit', 'ProjectsController@taskEdit')->name('task.edit');
    Route::post('projects/taskboard/{id}/update', 'ProjectsController@taskUpdate')->name('task.update');
    Route::delete('projects/taskboard/{id}/delete', 'ProjectsController@taskDestroy')->name('task.destroy');
    Route::get('projects/taskboard/{id}/show', 'ProjectsController@taskShow')->name('task.show');
    Route::post('projects/order', 'ProjectsController@order')->name('taskboard.order');

    Route::post('projects/{id}/taskboard/{tid}/comment', 'ProjectsController@commentStore')->name('comment.store');
    Route::post('projects/taskboard/{id}/file', 'ProjectsController@commentStoreFile')->name('comment.file.store');
    Route::delete('projects/taskboard/comment/{id}', 'ProjectsController@commentDestroy')->name('comment.destroy');
    Route::delete('projects/taskboard/file/{id}', 'ProjectsController@commentDestroyFile')->name('comment.file.destroy');

    Route::post('projects/taskboard/{id}/checklist/store', 'ProjectsController@checkListStore')->name('task.checklist.store');
    Route::post('projects/taskboard/{id}/checklist/{cid}/update', 'ProjectsController@checklistUpdate')->name('task.checklist.update');
    Route::delete('projects/taskboard/{id}/checklist/{cid}', 'ProjectsController@checklistDestroy')->name('task.checklist.destroy');

    Route::get('projects/{id}/client/{cid}/permission', 'ProjectsController@clientPermission')->name('client.permission');
    Route::post('projects/{id}/client/{cid}/permission/store', 'ProjectsController@storeClientPermission')->name('client.store.permission');

    Route::get('timesheet', 'ProjectsController@timeSheet')->name('task.timesheetRecord');
    Route::get('timesheet/create', 'ProjectsController@timeSheetCreate')->name('task.timesheet');
    Route::post('timesheet/create', 'ProjectsController@timeSheetStore')->name('task.timesheet.store');
    Route::get('timesheet/{tid}/edit', 'ProjectsController@timeSheetEdit')->name('task.timesheet.edit');
    Route::post('timesheet/{tid}/update', 'ProjectsController@timeSheetUpdate')->name('task.timesheet.update');
    Route::delete('timesheet/{tid}/destroy', 'ProjectsController@timeSheetDestroy')->name('task.timesheet.destroy');
    Route::post('timesheet/project/task', 'ProjectsController@projectTask')->name('timesheet.project.task');

    Route::post('projects/bug/kanban/order', 'ProjectsController@bugKanbanOrder')->name('bug.kanban.order');
    Route::get('projects/{id}/bug/kanban', 'ProjectsController@bugKanban')->name('task.bug.kanban');
    Route::get('projects/{id}/bug', 'ProjectsController@bug')->name('task.bug');
    Route::get('projects/{id}/bug/create', 'ProjectsController@bugCreate')->name('task.bug.create');
    Route::post('projects/{id}/bug/store', 'ProjectsController@bugStore')->name('task.bug.store');
    Route::get('projects/{id}/bug/{bid}/edit', 'ProjectsController@bugEdit')->name('task.bug.edit');
    Route::post('projects/{id}/bug/{bid}/update', 'ProjectsController@bugUpdate')->name('task.bug.update');
    Route::delete('projects/{id}/bug/{bid}/destroy', 'ProjectsController@bugDestroy')->name('task.bug.destroy');

    Route::get('projects/{id}/bug/{bid}/show', 'ProjectsController@bugShow')->name('task.bug.show');
    Route::post('projects/{id}/bug/{bid}/comment', 'ProjectsController@bugCommentStore')->name('bug.comment.store');
    Route::post('projects/bug/{bid}/file', 'ProjectsController@bugCommentStoreFile')->name('bug.comment.file.store');
    Route::delete('projects/bug/comment/{id}', 'ProjectsController@bugCommentDestroy')->name('bug.comment.destroy');
    Route::delete('projects/bug/file/{id}', 'ProjectsController@bugCommentDestroyFile')->name('bug.comment.file.destroy');

    Route::get('projects/all/show', 'ProjectsController@getAllProjects')->name('get.all.projects');
    Route::get('projectsByUser/{id}', 'ProjectsController@projectsByUser')->name('get.projects.by.user');

});

Route::post('calender/event/date', 'CalenderController@dropEventDate')->name('calender.event.date');
Route::resource('calender', 'CalenderController')->middleware(['auth','XSS',]);

Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::resource('bugstatus', 'BugStatusController');
    Route::post('/bugstatus/order', ['as' => 'bugstatus.order','uses' => 'BugStatusController@order',]);
});



Route::group(['middleware' => ['auth','XSS',],], function (){
    Route::resource('invoices', 'InvoiceController');

    Route::get('invoices/{id}/products', 'InvoiceController@productAdd')->name('invoices.products.add');
    Route::get('invoices/{id}/products/{pid}', 'InvoiceController@productEdit')->name('invoices.products.edit');
    Route::post('invoices/{id}/products', 'InvoiceController@productStore')->name('invoices.products.store');
    Route::post('invoices/{id}/products/{pid}/update', 'InvoiceController@productUpdate')->name('invoices.products.update');
    Route::delete('invoices/{id}/products/{pid}', 'InvoiceController@productDelete')->name('invoices.products.delete');
    Route::post('invoices/milestone/task', 'InvoiceController@milestoneTask')->name('invoices.milestone.task');

    Route::get('invoices-payments', 'InvoiceController@payments')->name('invoices.payments');
    Route::get('invoices/{id}/payments', 'InvoiceController@paymentAdd')->name('invoices.payments.create');
    Route::post('invoices/{id}/payments', 'InvoiceController@paymentStore')->name('invoices.payments.store');

    Route::get('invoice/{id}/custom-send', 'InvoiceController@customMail')->name('invoice.custom.send');
    Route::post('invoice/{id}/custom-mail', 'InvoiceController@customMailSend')->name('invoice.custom.mail');
    Route::get('invoice/{id}/payment/reminder', 'InvoiceController@paymentReminder')->name('invoice.payment.reminder');
    Route::get('invoice/{id}/sent', 'InvoiceController@sent')->name('invoice.sent');
    Route::get('/invoices/preview/{template}/{color}',['as' => 'invoice.preview','uses' =>'InvoiceController@previewInvoice']);
});

Route::get('invoices/{id}/get_invoice', 'InvoiceController@printInvoice')->name('get.invoice')->middleware(['XSS']);

// Estimation
Route::get('/estimations/{id}/products/{pid}',['as' => 'estimations.products.edit','uses' =>'EstimationController@productEdit'])->middleware(['auth','XSS']);
Route::post('/estimations/{id}/products/{pid}',['as' => 'estimations.products.update','uses' =>'EstimationController@productUpdate'])->middleware(['auth','XSS']);
Route::delete('/estimations/{id}/products/{pid}',['as' => 'estimations.products.delete','uses' =>'EstimationController@productDelete'])->middleware(['auth','XSS']);
Route::get('/estimations/{id}/products',['as' => 'estimations.products.add','uses' =>'EstimationController@productAdd'])->middleware(['auth','XSS']);
Route::post('/estimations/{id}/products',['as' => 'estimations.products.store','uses' =>'EstimationController@productStore'])->middleware(['auth','XSS']);
Route::get('estimations/{id}/get_estimation', 'EstimationController@printEstimation')->name('get.estimation')->middleware(['auth','XSS']);
Route::get('/estimations/preview/{template}/{color}',['as' => 'estimations.preview','uses' =>'EstimationController@previewEstimation']);
// end Estimation


// For Notification
Route::get('/{uid}/notification/seen', ['as' => 'notification.seen','uses' => 'UserController@notificationSeen',]);
// end for notification


// Email Templates
Route::get('email_template_lang/{id}/{lang?}', 'EmailTemplateController@manageEmailLang')->name('manage.email.language')->middleware(['auth']);
Route::post('email_template_store/{pid}', 'EmailTemplateController@storeEmailLang')->name('store.email.language')->middleware(['auth']);
Route::post('email_template_status/{id}', 'EmailTemplateController@updateStatus')->name('status.email.language')->middleware(['auth']);

Route::resource('email_template', 'EmailTemplateController')->middleware(['auth','XSS',]);
Route::resource('email_template_lang', 'EmailTemplateLangController')->middleware(['auth','XSS',]);
// End Email Templates

Route::resource('estimations', 'EstimationController');
Route::resource('taxes', 'TaxController')->middleware(['auth','XSS',]);
Route::resource('products', 'ProductsController')->middleware(['auth','XSS',]);
Route::resource('expenses', 'ExpenseController')->middleware(['auth','XSS',]);
Route::resource('payments', 'PaymentController')->middleware(['auth','XSS',]);
Route::resource('notes', 'NoteController')->middleware(['auth','XSS',]);


Route::post('/invoices/{id}/payment',['as' => 'client.invoice.payment','uses' =>'InvoiceController@addPayment'])->middleware(['auth', 'XSS']);
Route::post('/{id}/pay-with-paypal',['as' => 'client.pay.with.paypal','uses' =>'PaypalController@clientPayWithPaypal'])->middleware(['auth','XSS']);
Route::get('/{id}/get-payment-status',['as' => 'client.get.payment.status','uses' =>'PaypalController@clientGetPaymentStatus'])->middleware(['auth','XSS']);
Route::get('get/tasksList/{id}',['as' => 'get.tasksList','uses' =>'ProjectsController@taskBoardList'])->middleware(['auth','XSS']);
Route::get('getAllTasks',['as' => 'getAllTasks','uses' =>'ProjectsController@getAllTasks'])->middleware(['auth','XSS']);
