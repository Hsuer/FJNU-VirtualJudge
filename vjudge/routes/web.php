<?php

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

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['prefix' => 'problem'], function () {
    Route::get('/', 'ProblemController@index');
    Route::get('/problemlist', 'ProblemController@getProblemList')->name('problem.list');
    Route::get('/{id}', 'ProblemController@show')->where(['id' => '[0-9]+'])->name('problem.show');
    Route::get('/{id}/origin', 'ProblemController@origin')->where(['id' => '[0-9]+'])->name('problem.origin');
    Route::get('/{id}/rewrite', 'ProblemController@rewrite')->where(['id' => '[0-9]+'])->middleware('auth')->name('problem.rewrite');
    Route::post('/{id}/restore', 'ProblemController@restore')->where(['id' => '[0-9]+'])->middleware('auth')->name('problem.restore');
    Route::get('/{id}/submit', 'ProblemController@submit')->where(['id' => '[0-9]+'])->middleware('auth')->name('problem.submit');
    Route::get('/crawl/{str}','ProblemController@crawl');
    Route::get('/recrawl/{str}','ProblemController@recrawl');
    Route::get('/query/{str}', 'ProblemController@query');
});

Route::group(['prefix' => 'status'], function () {
    Route::get('/', 'StatusController@index')->name('status');
    Route::get('/statuslist', 'StatusController@getStatusList')->name('status.list');
    Route::get('/{id}', 'StatusController@show')->where(['id' => '[0-9]+'])->middleware('status.access')->name('status.show');
    Route::get('/get/{id}', 'StatusController@getStatus')->where(['id' => '[0-9]+']);
    Route::get('/rejudge/{id}', 'StatusController@rejudge')->where(['id' => '[0-9]+']);
    Route::post('/store', 'StatusController@store')->middleware('auth')->middleware('auth');
});

Route::group(['prefix' => 'contest'], function () {
    Route::get('/', 'ContestController@index')->name('contest.index');
    Route::get('/contestlist', 'ContestController@getContestList')->name('contest.list');
    Route::get('/create', 'ContestController@create')->middleware('auth');
    Route::post('/store', 'ContestController@store')->middleware('auth')->name('contest.store');
   
    Route::get('/{id}/password', 'ContestController@password')->where(['id' => '[0-9]+'])->name('contest.password');
    Route::post('/check_password/{id}', 'ContestController@check_password')->where(['id' => '[0-9]+'])->name('contest.check.password');
    Route::get('/{id}/countdowm', 'ContestController@countdown')->where(['id' => '[0-9]+'])->name('contest.countdown');

    Route::get('/{id}', 'ContestController@show')->where(['id' => '[0-9]+'])->middleware('contest.access')->name('contest.show');
    Route::get('/problemlist/{id}', 'ContestController@getProblemList')
        ->where(['id' => '[0-9]+'])->middleware('contest.access')->name('contest.problem.list');
    Route::get('/{id}/problem/{pid?}', 'ContestController@problem')
        ->where(['id' => '[0-9]+'])->middleware('contest.access')->name('contest.problem');
    Route::get('/{id}/problem/{pid?}/submit', 'ContestController@submit')
        ->where(['id' => '[0-9]+'])->middleware('contest.access')->middleware('auth')->name('contest.submit');
    Route::get('/{id}/status', 'ContestController@status')
        ->where(['id' => '[0-9]+'])->middleware('contest.access')->name('contest.status');
    Route::get('/{id}/statuslist', 'ContestController@getStatusList')->where(['id' => '[0-9]+'])->middleware('contest.access')->name('contest.status.list');
    Route::get('/{id}/standing', 'ContestController@standing')->where(['id' => '[0-9]+'])->middleware('contest.access')->name('contest.standing');
    Route::get('/{id}/edit', 'ContestController@edit')->where(['id' => '[0-9]+'])->middleware('auth')->middleware('contest.access')->name('contest.edit');
    Route::post('/{id}/update', 'ContestController@update')->where(['id' => '[0-9]+'])->middleware('auth')->middleware('contest.access')->name('contest.update');
});

Route::group(['prefix' => 'user'], function () {
    Route::get('/userlist', 'UserController@getUserList')->name('user.list');
    Route::get('/', 'UserController@index')->middleware('auth');
    Route::get('/modify', 'UserController@modify')->middleware('auth');
    Route::post('/modify_profile', 'UserController@modify_profile')->middleware('auth');
    Route::get('/password', 'UserController@password')->middleware('auth');
    Route::post('/change_password', 'UserController@change_password')->middleware('auth');
    Route::get('/data', 'UserController@data');
    Route::get('/submission', 'UserController@submission');
    Route::get('/{id}', 'UserController@show')->where(['id' => '[0-9]+'])->name('user.show');
    Route::get('/{id}/data', 'UserController@show_data')->where(['id' => '[0-9]+'])->name('user.show.data');
    Route::get('/{id}/submission', 'UserController@show_submission')->where(['id' => '[0-9]+'])->name('user.show.submission');
});

Route::get('/ranklist', function() {
    return view('users.ranklist');
});

Route::get('/tools', function() {
	return view('home');
});

Route::get('/error', function() {
    return view('error');
})->name('error');

Route::get('mail/sendReminderEmail/{id}','MailController@sendReminderEmail');