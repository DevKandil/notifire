<?php

namespace DevKandil\NotiFire\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FcmController extends Controller
{
    /**
     * Update the FCM token for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string|min:32'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $user->fcm_token = $request->fcm_token;
            $user->save();

            Log::info('FCM token updated successfully', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update FCM token', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM token'
            ], 500);
        }
    }
}