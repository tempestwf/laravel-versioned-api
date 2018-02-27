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
    public function __construct()
    {
        parent::__construct();

    }

    public function about(Request $request)
    {
        $data = [
            'success' => true,
            'health' => 'Up and running',
            'version' => env('APP_VESION', 'v1'),
            'message' => env('APP_DESCRIPTION', 'Welcome to Tempest Tools Skeleton!')
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
