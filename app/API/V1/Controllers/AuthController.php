<?php

namespace App\API\V1\Controllers;

use App;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Entities\User;
use Dingo\Api\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Hash;

class AuthController extends APIControllerAbstract
{

    /** @var JWTAuth $auth */
    protected  /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $auth;
    protected  $userRepo;

    public function __construct(UserRepository $userRepo)
	{
		parent::__construct();

		$this->auth = App::make(JWTAuth::class);
        $this->userRepo = $userRepo;
	}

    /**
     * User Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');

        /** @var User $user */
		$user = $this->userRepo->findOneBy(['email'=>$credentials['email']]);
        if (!Hash::check($credentials['password'], $user->getPassword())) {
            return response()->json(['error' => 'invalid_credentials'], 422);
        }

        if ($user->getEmailVerification()->getVerified() === false) {
            return response()->json(['error' => 'email_not_verified'], 403);
        }

        try
		{
			if(($token = $this->auth->attempt($credentials)) === FALSE)
			{
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch(JWTException $e)
		{
			return response()->json(['error' => 'could_not_create_token'], 500);
		}
		
		return response()->json(compact('token'));
	}

    /**
     * Refresh Tocken
     *
     * @return \Illuminate\Http\JsonResponse
     */
	public function refresh()
	{
		if($this->auth->getToken() === FALSE)
		{
			throw new BadRequestHttpException('token_absent');
		}
		
		try
		{
			$token = $this->auth->refresh();
		} catch(TokenInvalidException $e)
		{
			throw new UnauthorizedHttpException('token_invalid');
		}
		
		return response()->json(compact('token'));
	}

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'GET'=>[],
                'POST'=>[],
                'PUT'=>[],
                'DELETE'=>[]
            ]
        ];
    }
}
