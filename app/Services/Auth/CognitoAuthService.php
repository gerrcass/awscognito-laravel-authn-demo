<?php

namespace App\Services\Auth;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

class CognitoAuthService
{
    private CognitoIdentityProviderClient $client;

    public function __construct()
    {
        $this->client = new CognitoIdentityProviderClient([
            'region' => config('services.cognito.region'),
            'version' => 'latest',
        ]);
    }

    public function authenticate(string $username, string $password): array
    {
        $clientId = config('services.cognito.client_id');
        $clientSecret = config('services.cognito.client_secret');

        $secretHash = base64_encode(hash_hmac(
            'sha256',
            $username . $clientId,
            $clientSecret,
            true
        ));

        try {
            $result = $this->client->initiateAuth([
                'AuthFlow' => 'USER_PASSWORD_AUTH',
                'ClientId' => $clientId,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password,
                    'SECRET_HASH' => $secretHash,
                ],
            ]);

            // Handle NEW_PASSWORD_REQUIRED challenge
            if (isset($result['ChallengeName']) && $result['ChallengeName'] === 'NEW_PASSWORD_REQUIRED') {
                $result = $this->client->respondToAuthChallenge([
                    'ChallengeName' => 'NEW_PASSWORD_REQUIRED',
                    'ClientId' => $clientId,
                    'ChallengeResponses' => [
                        'USERNAME' => $username,
                        'NEW_PASSWORD' => $password,
                        'SECRET_HASH' => $secretHash,
                    ],
                    'Session' => $result['Session'],
                ]);
            }

            $idToken = $result['AuthenticationResult']['IdToken'] ?? null;
            $payload = $this->decodeJwtPayload($idToken);

            return [
                'success' => true,
                'sub' => $payload['sub'] ?? null,
                'username' => $payload['cognito:username'] ?? $username,
            ];
        } catch (AwsException $e) {
            $errorCode = $e->getAwsErrorCode();

            return [
                'success' => false,
                'error' => match ($errorCode) {
                    'NotAuthorizedException', 'UserNotFoundException' => 'Usuario o contraseña incorrectos',
                    'InvalidPasswordException' => 'La contraseña no cumple con los requisitos de seguridad',
                    default => 'Error de autenticación. Intente nuevamente',
                },
            ];
        }
    }

    private function decodeJwtPayload(?string $token): array
    {
        if (!$token) {
            return [];
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return [];
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        return is_array($payload) ? $payload : [];
    }
}