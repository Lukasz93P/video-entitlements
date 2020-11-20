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
    ['prefix' => 'entitlements'],
    function () {
        Route::group(
            ['prefix' => 'broadcasters', 'as' => 'broadcasters.'],
            function () {
                Route::group(
                    ['prefix' => '{broadcasterId}/plans', 'as' => 'plans.'],
                    function () {
                        Route::group(
                            ['prefix' => '{planId}/parents', 'as' => 'parents.'],
                            function () {
                                Route::put('/', ['uses' => 'BroadcastersPlansController@attachPlanToParent'])
                                    ->name('attach')
                                ;

                                Route::delete(
                                    '/{parentPlanId}',
                                    ['uses' => 'BroadcastersPlansController@detachPlanFromParent']
                                )
                                    ->name('detach')
                                ;
                            }
                        );

                        Route::group(
                            ['prefix' => '{planId}/categories', 'as' => 'categories.'],
                            function () {
                                Route::put('/', ['uses' => 'BroadcastersPlansController@assignCategoryToPlan'])
                                    ->name('assign')
                                ;

                                Route::delete(
                                    '/{categoryId}',
                                    ['uses' => 'BroadcastersPlansController@unassignCategoryFromPlan']
                                )
                                    ->name('unassign')
                                ;
                            }
                        );
                    }
                );

                Route::post('/', ['uses' => 'BroadcastersPlansController@registerBroadcaster'])
                    ->name('create')
                ;
            }
        );

        Route::group(
            ['prefix' => 'videos', 'as' => 'videos.'],
            function () {
                Route::group(
                    ['prefix' => '{videoId}/plans', 'as' => 'plans.'],
                    function () {
                        Route::put('/', ['uses' => 'VideosController@assignVideoToPlan'])
                            ->name('assign')
                        ;

                        Route::delete('/{planId}', ['uses' => 'VideosController@unassignVideoToPlan'])
                            ->name('unassign')
                        ;
                    }
                );

                Route::get('{videoId}/viewers/{viewerId}', 'VideosController@isViewerEntitledToWatchVideo')
                    ->name('viewers.entitlement')
                ;

                Route::group(
                    ['prefix' => '{videoId}/categories', 'as' => 'categories.'],
                    function () {
                        Route::put('/', ['uses' => 'VideosController@assignVideoToCategory'])
                            ->name('assign')
                        ;

                        Route::delete('/{categoryId}', ['uses' => 'VideosController@unassignVideoFromCategory'])
                            ->name('unassign')
                        ;
                    }
                );
            }
        );

        Route::group(
            ['prefix' => 'viewers', 'as' => 'viewers.'],
            function () {
                Route::put('{viewerId}/plans', ['uses' => 'ViewersController@registerViewerPurchasedPlan'])
                    ->name('plans.purchased')
                ;

                Route::put('{viewerId}/videos', ['uses' => 'ViewersController@registerViewerPurchasedPayPerViewVideo'])
                    ->name('videos.purchased')
                ;

                Route::post('/', ['uses' => 'ViewersController@registerViewer'])
                    ->name('create')
                ;
            }
        );
    }
);
