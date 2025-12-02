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

    protected function responseError($errors, $status = 400)
    {
        // return back()->with(['message' => 'record not found']);
        if (request()->wantsJson()) return response()->json($errors, $status);

        return back()->withErrors($errors);
    }

    protected function responseSuccess($record, $message)
    {
        if (request()->wantsJson()) return response()->json([
            'message' => $message,
            'record' => $record
        ]);

        return back()->with('success', $message);
    }
}
