<?php

namespace App\API\V1\Controllers;

use App\API\V1\Repositories\UserRepository;
use App\API\V1\Transformers\UserTransformer;
use TempestTools\Crud\Laravel\Controllers\RestfulControllerTrait;
use TempestTools\Crud\Orm\Transformers\ToArrayTransformer;

class UserController extends APIControllerAbstract
{
    use /** @noinspection TraitsPropertiesConflictsInspection */ RestfulControllerTrait;

    public function __construct(UserRepository $repo, ToArrayTransformer $arrayTransformer)
    {
        $this->setRepo($repo);
        $this->setTransformer($arrayTransformer);
        parent::__construct();
    }
    /** @noinspection SenselessMethodDuplicationInspection */

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [];
    }

	public function me()
	{
		return $this->response->item($this->getUser(), new UserTransformer());
	}
}