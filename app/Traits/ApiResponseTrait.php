<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     *
     * @param array $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success($data = [], $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ? __($message) : __('api.success'),
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function error($message, $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => __($message),
            'data' => null,
        ], $code);
    }

    /**
     * Return a validation error JSON response.
     *
     * @param array $errors
     * @param string|null $message
     * @return JsonResponse
     */
    protected function validationError($errors, $message = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message ? __($message) : __('api.validation_error'),
            'data' => [
                'errors' => $errors,
            ],
        ], 422);
    }

    /**
     * Format validation errors from Validator or ValidationException.
     *
     * @param \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\ValidationException $validator
     * @param string|null $message
     * @return JsonResponse
     */
    protected function formatValidationErrors($validator, $message = null): JsonResponse
    {
        $errors = [];
        
        if ($validator instanceof \Illuminate\Validation\ValidationException) {
            $errors = $validator->errors()->toArray();
        } elseif (method_exists($validator, 'errors')) {
            $errors = $validator->errors()->toArray();
        } elseif (is_array($validator)) {
            $errors = $validator;
        }

        return $this->validationError($errors, $message);
    }
}

