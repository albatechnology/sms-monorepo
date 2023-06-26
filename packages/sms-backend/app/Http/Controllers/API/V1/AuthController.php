<?php

namespace App\Http\Controllers\API\V1;

use App\Classes\DocGenerator\Enums\Tags;
use App\Http\Requests\API\V1\GetApiTokenRequest;
use App\Http\Resources\V1\User\TokenResource;
use App\Enums\UserType;
use App\Models\User;
use App\Models\PersonalAccessToken;
use App\OpenApi\Customs\Attributes as CustomOpenApi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class AuthController extends BaseApiController
{

    /**
     * Get Token
     *
     * Get a user token
     *
     * @param GetApiTokenRequest $request
     * @return mixed
     * @throws ValidationException
     */
    #[CustomOpenApi\Operation(id: 'authToken', tags: [Tags::Auth, Tags::V1])]
    #[CustomOpenApi\RequestBody(request: GetApiTokenRequest::class)]
    #[CustomOpenApi\Response(resource: TokenResource::class, statusCode: 200)]
    public function token(GetApiTokenRequest $request): mixed
    {
        $checkToken = PersonalAccessToken::where('plain_text_token', request()->bearerToken())->first();

        $user = User::where('email', $request->email)->first();

        if ($checkToken && ($checkToken->user->type->is(UserType::DIRECTOR) || $checkToken->user->is_admin)) {
            $validatePassword = true;
        } else {
            $validatePassword = Hash::check($request->password, $user->password);
        }

        if (!$user || !$validatePassword) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->tokens()->first()?->plain_text_token ?? $user->createToken('default')->plainTextToken;

        $user->setDefaultChannel();

        return TokenResource::make(['token' => $token]);
    }
}
