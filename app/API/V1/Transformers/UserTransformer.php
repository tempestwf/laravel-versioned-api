<?php

namespace App\API\V1\Transformers;

use App\API\V1\Entities\User as Entity;

use App\Transformers\Transformer;

class UserTransformer extends Transformer
{
	/**
	 * @param Entity $entity
	 *
	 * @return array
	 */
	protected $availableIncludes = array();
	
	/**
	 * @param Entity $entity
	 *
	 * @return array
	 */
	public function transform(Entity $entity)
	{
		if($this->verifyItem($entity) == TRUE)
		{
			return array(
				'id'    => $entity->getId(),
				'email' => $entity->getEmail(),
                'firstName' => $entity->getFirstName(),
                'middleInitial' => $entity->getMiddleInitial(),
                'lastName' => $entity->getLastName(),
                'age' => $entity->getAge(),
                'weight' => $entity->getWeight(),
                'height' => $entity->getHeight(),
                'gender' => $entity->getGender(),
                'phoneNumber' => $entity->getPhoneNumber(),
                'lifestyle' => $entity->getLifestyle(),
                'local' => $entity->getLocale(),
                'job' => $entity->getJob(),
                'createdAd' => $entity->getCreatedAt(),
                'deletedAt' => $entity->getDeletedAt()
			);
		}
		
		else
		{
			return array();
		}
	}
}