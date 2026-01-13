<?php

declare(strict_types=1);

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
        $token = $request->input('action_token') ?? $request->header('X-Action-Token');

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
            // Get record and data from request (handle both JSON body and query params)
            $record = $request->input('record');
            $data = $request->input('data', []);

            // For GET requests, record and data may be JSON-encoded strings
            if (is_string($record) && $record !== '') {
                $decoded = json_decode($record, false);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $record = $decoded;
                }
            }

            // Convert array record to object for consistent access in closures ($record->id vs $record['id'])
            if (is_array($record)) {
                $record = (object) $record;
            }

            if (is_string($data) && $data !== '') {
                $decoded = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $decoded;
                }
            }

            // Execute the action
            $result = $closure($record, $data);

            // Check if result contains a redirect
            $hasRedirect = $result instanceof \Illuminate\Http\RedirectResponse
                || (is_array($result) && isset($result['redirect']));

            // Only flush notifications if there's NO redirect
            // If there's a redirect, notifications will be shown from session after page loads
            $notifications = [];
            if (! $hasRedirect && app()->bound('accelade.notify')) {
                $notifyManager = app('accelade.notify');
                $flushed = $notifyManager->flush();
                $notifications = $flushed->map(fn ($n) => $n->jsonSerialize())->values()->toArray();
            }

            // Build base response
            $response = [
                'success' => true,
                'notifications' => $notifications,
            ];

            // Handle different return types
            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                $response['redirect'] = $result->getTargetUrl();

                return response()->json($response);
            }

            if (is_string($result)) {
                $response['message'] = $result;

                return response()->json($response);
            }

            if (is_array($result)) {
                return response()->json([...$response, ...$result]);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            // Flush notifications on error too
            if (app()->bound('accelade.notify')) {
                app('accelade.notify')->flush();
            }

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : __('actions::actions.errors.execution_failed'),
            ], 500);
        }
    }
}
