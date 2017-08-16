<?php

namespace App\API\V1\Controllers;

use App\API\V1\Transformers\UserTransformer;

class UserController extends APIControllerAbstract
{
	public function me()
	{
		return $this->response->item($this->getUser(), new UserTransformer());
	}
}