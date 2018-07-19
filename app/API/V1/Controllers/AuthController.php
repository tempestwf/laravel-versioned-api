<?php

namespace App\API\V1\Controllers;

use App;
use App\API\V1\Repositories\RoleRepository;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Repositories\LoginAttemptRepository;
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
    /** @var LoginAttemptRepository **/
    protected  $loginAttemptRepo;

    /**
     * AuthController constructor.
     * @param UserRepository $userRepo
     * @param Socialite $socialite
     * @param RoleRepository $roleRepo
     * @param LoginAttemptRepository $loginAttemptRepo
     */
    public function __construct(UserRepository $userRepo, Socialite $socialite, RoleRepository $roleRepo, LoginAttemptRepository $loginAttemptRepo)
	{
		parent::__construct();

		$this->auth = App::make(JWTAuth::class);
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
        $this->socialite = $socialite;
        $this->loginAttemptRepo = $loginAttemptRepo;
	}

    /**
     * User Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
	public function authenticate(Request $request)
	{
        $credentials = $request->only('email', 'password');
        $attemptResult = null;
        $token = null;

        /** @var User $user */
		$user = $this->userRepo->findOneBy(['email'=>$credentials['email']]);
		if ($user) {
            $attemptResult = $this->loginAttemptRepo->attemptCheck($user);
            if (!$attemptResult) {
                /** Login Attempt */
                try {
                    if (!Hash::check($credentials['password'], $user->getPassword()) || ($token = $this->auth->attempt($credentials)) === false) {
                        $attemptResult = $this->loginAttemptRepo->logAttempt($user, $credentials, LoginAttemptRepository::LOGIN_ATTEMPT_INVALID_PASSWORD);
                    } else {
                        $this->loginAttemptRepo->resetUserAttempt($user);
                    }
                } catch(JWTException $e) {
                    $attemptResult = $this->loginAttemptRepo->logAttempt($user, $credentials, LoginAttemptRepository::LOGIN_ATTEMPT_COULD_NOT_CREATE_TOKEN);
                }

                /** Check for activation */
                if ($token && $user->isActivated() === false) {
                    $attemptResult = $this->loginAttemptRepo->logAttempt($user, $credentials, LoginAttemptRepository::LOGIN_ATTEMPT_NOT_ACTIVATED);
                }
            }
        } else {
            $attemptResult = LoginAttemptRepository::LOGIN_ATTEMPT_INVALID_EMAIL;
        }

        switch ($attemptResult) {
            case LoginAttemptRepository::LOGIN_ATTEMPT_INVALID_EMAIL:
            case LoginAttemptRepository::LOGIN_ATTEMPT_INVALID_PASSWORD:
            case LoginAttemptRepository::LOGIN_ATTEMPT_INVALID_CREDENTIALS:
                return response()->json(['error' => trans('auth.invalid_credentials')], 401);
                break;
            case LoginAttemptRepository::LOGIN_ATTEMPT_ERROR_ACCOUNT_PARTIAL_LOCKED:
                return response()->json(['error' => trans('auth.attempt_partial_lock')], 401);
                break;
            case LoginAttemptRepository::LOGIN_ATTEMPT_ERROR_ACCOUNT_FULL_LOCKED:
                return response()->json(['error' => trans('auth.attempt_full_lock')], 401);
                break;
            case LoginAttemptRepository::LOGIN_ATTEMPT_NOT_ACTIVATED:
                return response()->json(['error' => trans('auth.email_not_activated')], 403);
                break;
            case LoginAttemptRepository::LOGIN_ATTEMPT_COULD_NOT_CREATE_TOKEN:
                return response()->json(['error' => trans('auth.could_not_create_token')], 500);
                break;
            default:
                return response()->json(compact('token'));
        }
	}

    /**
     * Refresh Token
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
     * @throws \Exception
     */
    public function getSocialAuthCallback(string $provider)
    {
        $token = null;
        if($socializeUser = $this->socialite->driver($provider)->stateless()->user()) {
            $user = $this->userRepo->findOneBy(['email' => $socializeUser->email]);
            if (!$user) {
                /** Generate the user based on the socialite data **/
                $this->userRepo->registerSocializeUser($provider, $socializeUser);
                /** @var User $user */
                $user = $this->userRepo->findOneBy(['email' => $socializeUser->email]);
                /** Set user's default role **/
                $this->roleRepo->addUserRoles($user);
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
