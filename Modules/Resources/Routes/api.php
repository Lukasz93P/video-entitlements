<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    ['prefix' => 'resources'],
    function () {
        Route::group(
            ['prefix' => 'broadcasters/{broadcasterId}', 'as' => 'broadcasters.'],
            function () {
                Route::group(
                    ['prefix' => 'plans', 'as' => 'plans.'],
                    function () {
                        Route::post('/', ['uses' => 'PlansController@addNewPlan'])
                            ->name('create')
                        ;

                        Route::get('/', ['uses' => 'PlansController@getBroadcastersPlans'])
                            ->name('get')
                        ;
                    }
                );

                Route::group(
                    ['prefix' => 'videos', 'as' => 'videos.'],
                    function () {
                        Route::post('/', ['uses' => 'VideosController@addNewVideo'])
                            ->name('create')
                        ;

                        Route::get('/', ['uses' => 'VideosController@getBroadcastersVideos'])
                            ->name('get')
                        ;
                    }
                );
            }
        );
    }
);
