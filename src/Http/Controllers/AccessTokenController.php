<?php

namespace Ekopras18\LumenPassport\Http\Controllers;

use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use App\Models\User;
use Laminas\Diactoros\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use Ekopras18\LumenPassport\LumenPassport;
use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;

/**
 * Class AccessTokenController
 * @package Dusterio\LumenPassport\Http\Controllers
 */
class AccessTokenController extends \Laravel\Passport\Http\Controllers\AccessTokenController
{
    protected $accessTokenFactory;
    public function __construct(
        PersonalAccessTokenFactory $accessTokenFactory,
        AuthorizationServer $server,
        TokenRepository $tokens
    ) {
        $this->accessTokenFactory = $accessTokenFactory;
        parent::__construct($server, $tokens);
    }
    /**
     * Authorize a client to access the user's account.
     *
     * @param  ServerRequestInterface  $request
     * @return Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        $response = $this->withErrorHandling(function () use ($request) {
            $input = (array) $request->getParsedBody();
            $clientId = isset($input['client_id']) ? $input['client_id'] : null;
            $clientSecret = isset($input['client_secret']) ? $input['client_secret'] : null;
            $email = isset($input['username']) ? $input['username'] : null;

            $user = User::where('email', $email)->first();
            if (!$user) {
                return $this->customErrorHandling('invalid_username', 'Username not found', 'The user credentials are incorrect.');
            }

            $client = Client::where('user_id', $user->id)->first();

            if(!$client) {
                return $this->customErrorHandling('invalid_client', 'Client not found', 'The client credentials are incorrect.');
            }

            if ($client->id !== $clientId) {
                return $this->customErrorHandling('invalid_client_id', 'Client id not match', 'The client id credentials not match.');
            }

            if ($client->secret !== $clientSecret) {
                return $this->customErrorHandling('invalid_client_secret', 'Client secret not match', 'The client secret credentials not match.');
            }

            // Overwrite password grant at the last minute to add support for customized TTLs
            $this->server->enableGrantType(
                $this->makePasswordGrant(), LumenPassport::tokensExpireIn(null, $clientId)
            );

            return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        });

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            return $response;
        }

        $payload = json_decode($response->getBody()->__toString(), true);

        if (isset($payload['access_token'])) {

            $token = $this->accessTokenFactory->findAccessToken($payload);

            if (!$token instanceof Token) {
                return $response;
            }

            if ($token->client->firstParty() && LumenPassport::$allowMultipleTokens) {
                // We keep previous tokens for password clients
            } else {
                $this->revokeOrDeleteAccessTokens($token, $token->id);
            }
        }

        return $response;
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    private function makePasswordGrant()
    {
        $grant = new \League\OAuth2\Server\Grant\PasswordGrant(
            app()->make(\Laravel\Passport\Bridge\UserRepository::class),
            app()->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    /**
     * Revoke the user's other access tokens for the client.
     *
     * @param  Token $token
     * @param  string $tokenId
     * @return void
     */
    protected function revokeOrDeleteAccessTokens(Token $token, $tokenId)
    {
        $query = Token::where('user_id', $token->user_id)->where('client_id', $token->client_id);

        if ($tokenId) {
            $query->where('id', '<>', $tokenId);
        }

        $query->update(['revoked' => true]);
    }

    protected function customErrorHandling($error, $description, $message)
    {
        return response()->json([
            'error' => $error,
            'error_description' => $description,
            'message' => $message
        ], 401);
    }
}
