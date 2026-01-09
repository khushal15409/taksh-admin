<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class LogisticApiService
{
    private $baseUrl;
    private $endpoint;
    private $timeout;
    private $defaultRole;

    public function __construct()
    {
        $this->baseUrl = config('logistic.api.base_url', 'https://logistic.takshallinone.in');
        $this->endpoint = config('logistic.api.create_user_with_role_endpoint', '/api/v1/users/create-with-role');
        $this->timeout = config('logistic.timeout', 30);
        $this->defaultRole = config('logistic.default_role', 'lm-center');
    }

    /**
     * Get the full API URL for creating user with role
     * 
     * @return string
     */
    private function getApiUrl(): string
    {
        $baseUrl = rtrim($this->baseUrl, '/');
        $endpoint = ltrim($this->endpoint, '/');
        return $baseUrl . '/' . $endpoint;
    }

    /**
     * Create user with role in logistic system
     * 
     * @param string $email
     * @param string $mobileNumber
     * @param string $role
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public function createUserWithRole(string $email, string $mobileNumber, string $role = null): array
    {
        // Use default role if not provided
        $role = $role ?? $this->defaultRole;
        $apiUrl = $this->getApiUrl();

        try {
            Log::info('Calling logistic API to create user with role', [
                'email' => $email,
                'mobile_number' => $mobileNumber,
                'role' => $role,
                'url' => $apiUrl,
            ]);

            $response = Http::timeout($this->timeout)
                ->post($apiUrl, [
                    'email' => $email,
                    'mobile_number' => $mobileNumber,
                    'role' => $role,
                ]);

            $statusCode = $response->status();
            $responseBody = $response->json();
            $responseText = $response->body();

            Log::info('Logistic API response', [
                'status_code' => $statusCode,
                'response' => $responseBody,
            ]);

            // Check if request was successful (2xx status codes)
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'User created successfully in logistic system',
                    'data' => $responseBody,
                ];
            }

            // Handle error responses
            $errorMessage = 'Failed to create user in logistic system';
            
            if (isset($responseBody['message'])) {
                $errorMessage = $responseBody['message'];
            } elseif (isset($responseBody['error'])) {
                $errorMessage = is_string($responseBody['error']) 
                    ? $responseBody['error'] 
                    : json_encode($responseBody['error']);
            } elseif (!empty($responseText)) {
                // Try to extract error message from response
                $errorMessage = 'API Error: ' . $responseText;
            } else {
                $errorMessage = "API returned status code: {$statusCode}";
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'data' => $responseBody,
                'status_code' => $statusCode,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Logistic API connection error', [
                'error' => $e->getMessage(),
                'email' => $email,
                'mobile_number' => $mobileNumber,
            ]);

            return [
                'success' => false,
                'message' => 'Unable to connect to logistic system. Please try again later.',
                'data' => null,
            ];
        } catch (Exception $e) {
            Log::error('Logistic API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $email,
                'mobile_number' => $mobileNumber,
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while creating user in logistic system: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }
}

