<?php

use App\Http\Controllers\Api\V1\DeepLinkController;

/**
 * @route GET /api/v1/b/{id}
 * @desc Resolve a battle deep link.
 */
Route::prefix("v1")->group(function () {
    Route::get("b/{id}", [DeepLinkController::class, "show"]);
});