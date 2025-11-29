<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Mobile App API"
 * )
 * @OA\Components(
 *     @OA\Parameter(
 *         parameter="acceptJson",
 *         name="Accept",
 *         in="header",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             default="application/json"
 *         )
 *     )
 * )
 */
abstract class Controller
{
    //
}
