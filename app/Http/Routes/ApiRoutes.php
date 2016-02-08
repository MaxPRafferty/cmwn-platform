<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Event::listen('illuminate.query', function($query)
// {
//     var_dump($query);
// });

Route::get('/csrf_token', 'Api\ApiController@getToken');
Route::get('/parms/{parm_name}', function ($parm_name) {
    return \Config::get('mycustomvars.'.$parm_name);
});
Route::post('/auth/login', 'Api\AuthController@authenticate');
Route::post('/auth/reset', 'Auth\PasswordController@reset');
Route::get('/auth/logout', 'Auth\AuthController@getLogout');
Route::post('/create_demo_teacher', 'Api\UserController@createDemoTeacher');
Route::post('/create_demo_student', 'Api\UserController@createDemoStudent');
Route::post('/images/webhook', 'Api\ImagesController@webhook');

// The use must be a site admin to use these routes.
Route::group(['middleware' => ['auth', 'siteadmin']], function ($router) {
    Route::post('/users', 'Api\UserController@create');
    Route::post('/users/{uuid}/groups', 'Api\UserController@addToGroup');
    Route::post('/groups', 'Api\GroupController@create');
    Route::post('/districts', 'Api\DistrictController@create');
    Route::post('/organizations', 'Api\OrganizationController@create');
});

//The user must be a logged in to use the rest of these routes.
Route::group(['middleware' => 'auth'], function ($router) {

    Route::post('/auth/password', 'Api\AuthController@updatePassword'); // TODO move this to users/{uuid}/password

    Route::get('/parms/{parm_name}', function ($parm_name) {
        return \Config::get('mycustomvars.'.$parm_name);
    })->where('parm_name', '[a-z]+');

    Route::get('/sidebar', 'Api\MasterController@sidebar');
    Route::get('/friends', 'Api\UserController@friends');
    Route::post('/friends', 'Api\FriendshipController@handleFriends');
    Route::get('/auth/logout', 'Api\AuthController@logout');

    Route::get('/users', 'Api\UserController@index');
    Route::get('/users/{uuid}', 'Api\UserController@show');
    Route::post('/users/{uuid}', 'Api\UserController@update');

    Route::get('/users/{id}/groups', 'Api\UserController@getGroups');

    //User Images
    Route::get('/users/{id}/image', 'Api\UserController@showImage');
    Route::post('/users/{uuid}/image', 'Api\UserController@updateImage');
    Route::delete('/users/{id}/image', 'Api\UserController@deleteImage');

    Route::get('/suggestedfriends', 'Api\SuggestedController@show');

    Route::get('/users/sendfriendrequest', 'Api\FriendshipController@show');
    Route::post('/users/acceptfriendrequest/{id}', 'Api\FriendshipController@accept');
    Route::post('/users/rejectfriendrequest/{id}', 'Api\FriendshipController@reject');

    //Get Groups
    Route::get('/groups', 'Api\GroupController@index');
    Route::post('/groups/{uuid}/image', 'Api\GroupController@updateImage');
    Route::get('/groups/{uuid}', 'Api\GroupController@show');
    Route::get('/groups/{uuid}/users', 'Api\GroupController@getUsers');

    //Post Groups
    Route::post('/groups/{uuid}', ['uses' => 'Api\GroupController@update']);

    //Get Districts
    Route::get('/districts', 'Api\DistrictController@index');
    Route::get('/districts/{uuid}', 'Api\DistrictController@show');

    //Post Districts
    Route::post('/districts/{uuid}', ['uses' => 'Api\DistrictController@update']);
    Route::post('/districts/{uuid}/image', 'Api\DistrictController@updateImage');

    //Get Organizations
    Route::get('/organizations', 'Api\OrganizationController@index');
    Route::get('/organizations/{uuid}', 'Api\OrganizationController@show');
    Route::post('/organizations/{uuid}/image', 'Api\OrganizationController@updateImage');

    Route::post('/organizations/{uuid}', ['uses' => 'Api\OrganizationController@update']);

    Route::get('/roles', 'Api\RoleController@index');
    Route::get('/roles/{id}', 'Api\RoleController@show');

    //Games
    Route::get('/games', 'Api\GameController@index');
    Route::get('/games/{id}', 'Api\GameController@show');
    Route::post('/games/{id}', 'Api\GameController@update');
    Route::delete('/games/{id}', 'Api\GameController@delete');

    //Flips
    Route::get('/flips', 'Api\FlipController@index');
    Route::get('/flips/{id}', 'Api\FlipController@show');
    Route::post('/flips/{id}', 'Api\FlipController@update');
    Route::delete('/flips/{id}', 'Api\FlipController@delete');

    //Admin tasks: Import Excel files and update the DB.
    Route::post('/admin/importexcel', ['uses' => 'Api\MasterController@importExcel']);

});
