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
            return $this->errorResponse(__('actions::actions.errors.missing_token'));
        }

        $closureOrError = $this->resolveActionClosure($token);

        if ($closureOrError instanceof JsonResponse) {
            return $closureOrError;
        }

        return $this->executeAction($closureOrError, $request);
    }

    /**
     * Resolve the action closure from the encrypted token.
     */
    protected function resolveActionClosure(#[\SensitiveParameter] string $token): callable|JsonResponse
    {
        try {
            $payload = Crypt::decrypt($token);
        } catch (\Exception $e) {
            return $this->errorResponse(__('actions::actions.errors.invalid_token'));
        }

        $actionId = $payload['action_id'] ?? null;

        if (! $actionId) {
            return $this->errorResponse(__('actions::actions.errors.invalid_payload'));
        }

        $closure = session()->get("actions.{$actionId}");

        if (! $closure) {
            return $this->errorResponse(__('actions::actions.errors.action_expired'));
        }

        return $closure;
    }

    /**
     * Execute the action closure and return the response.
     */
    protected function executeAction(callable $closure, Request $request): JsonResponse
    {
        try {
            [$record, $data] = $this->parseRequestData($request);
            $result = $closure($record, $data);

            return $this->buildSuccessResponse($result);
        } catch (\Exception $e) {
            $this->flushNotifications();

            return $this->errorResponse(
                config('app.debug') ? $e->getMessage() : __('actions::actions.errors.execution_failed'),
                500
            );
        }
    }

    /**
     * Parse record and data from the request.
     *
     * @return array{mixed, array<string, mixed>}
     */
    protected function parseRequestData(Request $request): array
    {
        $record = $request->input('record');
        $data = $request->input('data', []);

        $record = $this->decodeJsonInput($record, false);
        $data = $this->decodeJsonInput($data, true);

        if (is_array($record)) {
            $record = (object) $record;
        }

        return [$record, is_array($data) ? $data : []];
    }

    /**
     * Decode a JSON-encoded input if necessary.
     */
    protected function decodeJsonInput(mixed $input, bool $associative): mixed
    {
        if (! is_string($input) || $input === '') {
            return $input;
        }

        $decoded = json_decode($input, $associative);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $input;
    }

    /**
     * Build a success response from the action result.
     */
    protected function buildSuccessResponse(mixed $result): JsonResponse
    {
        $hasRedirect = $result instanceof \Illuminate\Http\RedirectResponse
            || (is_array($result) && isset($result['redirect']));

        $notifications = $hasRedirect ? [] : $this->flushNotifications();

        $response = [
            'success' => true,
            'notifications' => $notifications,
        ];

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
    }

    /**
     * Flush and return notifications from the notification manager.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function flushNotifications(): array
    {
        if (! app()->bound('accelade.notify')) {
            return [];
        }

        $notifyManager = app('accelade.notify');
        $flushed = $notifyManager->flush();

        return $flushed->map(fn ($n) => $n->jsonSerialize())->values()->toArray();
    }

    /**
     * Return a JSON error response.
     */
    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
