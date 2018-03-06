<?php

namespace App\API\V1\Controllers;

use App;
use App\API\V1\Repositories\RoleRepository;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Entities\User;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Dingo\Api\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;
use Hash;

class AuthController extends APIControllerAbstract
{

    /** @var JWTAuth $auth **/
    protected  /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $auth;
    /** @var UserRepository **/
    protected  $userRepo;
    /** @var RoleRepository **/
    protected  $roleRepo;
    /** @var Socialite **/
    protected  $socialite;

    /**
     * AuthController constructor.
     * @param UserRepository $userRepo
     * @param Socialite $socialite
     * @param RoleRepository $roleRepo
     */
    public function __construct(UserRepository $userRepo, Socialite $socialite, RoleRepository $roleRepo)
	{
		parent::__construct();

		$this->auth = App::make(JWTAuth::class);
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
        $this->socialite = $socialite;
	}

    /**
     * User Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function authenticate(Request $request)
	{
        $token = null;
		$credentials = $request->only('email', 'password');

        /** @var User $user */
		$user = $this->userRepo->findOneBy(['email'=>$credentials['email']]);
        if (!Hash::check($credentials['password'], $user->getPassword())) {
            return response()->json(['error' => trans('auth.invalid_credentials')], 422);
        }

        if ($user->isActivated() === false) {
            return response()->json(['error' => trans('auth.email_not_activated')], 403);
        }

        try {
			if(($token = $this->auth->attempt($credentials)) === false) {
				return response()->json(['error' => trans('auth.invalid_credentials')], 401);
			}
		} catch(JWTException $e) {
			return response()->json(['error' => trans('auth.could_not_create_token')], 500);
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
		if($this->auth->getToken() === FALSE) {
			throw new BadRequestHttpException(trans('auth.token_absent'));
		}
		
		try {
			$token = $this->auth->refresh();
		} catch(TokenInvalidException $e) {
			throw new UnauthorizedHttpException(trans('auth.token_invalid'));
		}
		
		return response()->json(compact('token'));
	}

    /**
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSocialAuth(string $provider)
    {
        if(!config("services.$provider"))
            throw new BadRequestHttpException(trans('auth.no_such_provider'));

        /** @var \Laravel\Socialite\Two\FacebookProvider $redirect */
        $redirect = $this->socialite->driver($provider)->stateless();
        $result = [
            'provider' => $provider,
            'link' => $redirect->redirect()->getTargetUrl()
        ];

        return response()->json(compact('result'));
    }

    /**
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function getSocialAuthCallback(string $provider)
    {
        $token = null;
        if($socializeUser = $this->socialite->driver($provider)->stateless()->user()) {
            $user = $this->userRepo->findOneBy(['email' => $socializeUser->email]);
            if (!$user) {
                /** Generate the user based on the socialite data **/
                $this->userRepo->registerSocializeUser($provider, $socializeUser);
                $user = $this->userRepo->findOneBy(['email' => $socializeUser->email]);
                /** Set user's default role **/
                $this->roleRepo->setUserPermissions($user);
            }

            $credentials = [
                'email' => $user->getEmail(),
                'password' => Hash::make($user->getSocialize()->getToken())
            ];
            if ($user) {
                try {
                    if(($token = $this->auth->attempt($credentials)) === FALSE) {
                        return response()->json(['error' => trans('auth.invalid_credentials')], 401);
                    }
                } catch(JWTException $e) {
                    return response()->json(['error' => trans('auth.could_not_create_token')], 500);
                }
            }
        } else {
            throw new BadRequestHttpException(trans('auth.something_went_wrong'));
        }

        return response()->json(compact('token'));
    }

    public function forgotPassword(Request $request)
    {
        $token = null;
        $credentials = $request->only('email');

        /** @var User $user */
        $user = $this->userRepo->findOneBy(['email'=>$credentials['email']]);
        if (!$user) {
            return response()->json(['error' => trans('auth.invalid_email_credentials')], 422);
        }

        if ($user->isActivated() === false) {
            return response()->json(['error' => trans('auth.email_not_activated')], 403);
        }


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
