<?php

namespace Accelade\Actions\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;

class ActionController extends Controller
{
    /**
     * Execute an action from its encrypted token.
     */
    public function execute(Request $request): JsonResponse
    {
        $token = $request->input('_token') ?? $request->header('X-Action-Token');

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => __('actions::actions.errors.missing_token'),
            ], 400);
        }

        try {
            $payload = Crypt::decrypt($token);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('actions::actions.errors.invalid_token'),
            ], 400);
        }

        $actionId = $payload['action_id'] ?? null;

        if (! $actionId) {
            return response()->json([
                'success' => false,
                'message' => __('actions::actions.errors.invalid_payload'),
            ], 400);
        }

        // Retrieve the action closure from session
        $closure = session()->get("actions.{$actionId}");

        if (! $closure) {
            return response()->json([
                'success' => false,
                'message' => __('actions::actions.errors.action_expired'),
            ], 400);
        }

        try {
            // Get record and data from request
            $record = $request->input('record');
            $data = $request->input('data', []);

            // Execute the action
            $result = $closure($record, $data);

            // Clear the action from session (one-time use)
            session()->forget("actions.{$actionId}");

            // Handle different return types
            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                return response()->json([
                    'success' => true,
                    'redirect' => $result->getTargetUrl(),
                ]);
            }

            if (is_string($result)) {
                return response()->json([
                    'success' => true,
                    'message' => $result,
                ]);
            }

            if (is_array($result)) {
                return response()->json([
                    'success' => true,
                    ...$result,
                ]);
            }

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : __('actions::actions.errors.execution_failed'),
            ], 500);
        }
    }
}
