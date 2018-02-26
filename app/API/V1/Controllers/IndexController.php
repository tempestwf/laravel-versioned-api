<?php

namespace App\API\V1\Controllers;

use Dingo\Api\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

use App;

class IndexController extends APIControllerAbstract
{

    /** @var JWTAuth $auth */
    protected  /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $auth;

    public function __construct()
    {
        parent::__construct();

        $this->auth = App::make(JWTAuth::class);
    }

    public function about(Request $request)
    {
        $data = [
            'success' => true,
            'health' => 'Up and running',
            'version' => 'v1',
            'message' => 'Welcome to AKI API!'
        ];
        return response()->json(compact('data'));
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
