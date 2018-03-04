<?php

namespace App\Adapters;

use App\Entities\Entity;
use App\Entities\User;
use App\Guards\JWTGuard;
use Illuminate\Auth\AuthManager;
use Tymon\JWTAuth\Providers\User\UserInterface;

class UserAdapter implements UserInterface
{
	/**
	 * @var UserInterface $provider
	 */
	protected $provider;

    /**
     * UserAdapter constructor.
     * @param User $user
     */
	public function __construct(User $user)
	{
	}
	
	/**
	 * Get the user by the given key, value.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return Entity|null
	 */
	public function getBy($key, $value)
	{
		return $this->provider->getBy($key, $value);
	}
}
