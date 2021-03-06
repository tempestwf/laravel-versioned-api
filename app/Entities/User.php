<?php

namespace App\Entities;

use App\Entities\Traits\Authenticatable;
use App\API\V1\Traits\Entities\Blameable;
use TempestTools\Common\Entities\Traits\SoftDeleteable;
use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;

use Doctrine\ORM\Mapping AS ORM;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Entity implements AuthenticatableContract
{
	use Authenticatable, Blameable, SoftDeleteable, IpTraceable, Timestampable;
	
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(name="id", type="integer")
	 * @var int $id
	 */
	public $id;
	
	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}
}