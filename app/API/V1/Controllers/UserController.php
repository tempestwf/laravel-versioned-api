<?php

namespace App\API\V1\Controllers;

use App\API\V1\Entities\EmailVerification;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Repositories\RoleRepository;
use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Transformers\UserTransformer;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;

/** @noinspection LongInheritanceChainInspection */
class UserController extends APIControllerAbstract
{
    /** @var RoleRepository **/
    //private $roleRepo;

    /** @var EmailVerificationRepository **/
    //private $emailVerificationRepo;

    /**
     * UserController constructor.
     *
     * @param UserRepository $repo
     * @param ToArrayTransformer $arrayTransformer
     */
    public function __construct(UserRepository $repo, ToArrayTransformer $arrayTransformer/*, RoleRepository $roleRepo, EmailVerificationRepository $emailVerificationRepo*/)
    {
        $this->setRepo($repo);
        $this->setTransformer($arrayTransformer);
        parent::__construct();

        //$this->roleRepo = $roleRepo;
        //$this->emailVerificationRepo = $emailVerificationRepo;
    }
    /** @noinspection SenselessMethodDuplicationInspection */

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        // No special rules for this controller
        return [
            'default'=>[
                'GET'=>[],
                'POST'=>[],
                'PUT'=>[],
                'DELETE'=>[]
            ]
        ];
    }

    /**
     * Includes a special me action to get info about the currently logged in user (default functionality of the skeleton)
     * @return \Dingo\Api\Http\Response
     */
    public function me()
	{
		return $this->response->item($this->getUser(), new UserTransformer());
	}
}