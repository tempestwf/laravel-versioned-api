<?php
/**
 * Created by PhpStorm.
 * User: monxe
 * Date: 09/07/2018
 * Time: 10:40 PM
 */

namespace App\API\V1\Repositories;

use App\API\V1\Entities\LoginAttempt;
use App\API\V1\Entities\User;
use App\Repositories\Repository;
use Carbon\Carbon;

/** @noinspection LongInheritanceChainInspection */
class LoginAttemptRepository extends Repository
{
    const LOGIN_ATTEMPT_DEFAULT = 0;
    const LOGIN_ATTEMPT_INVALID_EMAIL = 1;
    const LOGIN_ATTEMPT_INVALID_PASSWORD = 2;
    const LOGIN_ATTEMPT_INVALID_CREDENTIALS = 3;
    const LOGIN_ATTEMPT_NOT_ACTIVATED = 4;
    const LOGIN_ATTEMPT_COULD_NOT_CREATE_TOKEN = 5;
    const LOGIN_ATTEMPT_ERROR_ACCOUNT_PARTIAL_LOCKED = 6;
    const LOGIN_ATTEMPT_ERROR_ACCOUNT_FULL_LOCKED = 7;

    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = LoginAttempt::class;

    /**
     * Log User Attempt
     *
     * @param User $user
     * @param null $value
     * @param int $error
     * @return int
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logAttempt(User $user, $value = null, int $error) :int
    {
        /** @var LoginAttempt $attempt */
        $attempt = $this->findOneBy(['user'=>$user]);
        $value['error'] = $error;
        if ($attempt) {
            $attempt = $this->update(
                [
                    'id' => $attempt->getId(),
                    'count' => $attempt->getCount() + 1,
                    'trace' => $value
                ],
                [],
                ['simplifiedParams' => true]);
        } else {
            $attempt = $this->create(
                [
                    'count' => 1,
                    'trace' => $value,
                    'user' => $user->getId()
                ],
                [],
                ['simplifiedParams' => true]);
        }

        $status = $this->getAttemptStatus($attempt[0]);
        return  $status ? $status : $error;
    }

    /**
     * Reset Attempts
     *
     * @param LoginAttempt $attempt
     * @param int $fullLockCount
     * @return mixed
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetAttempt(LoginAttempt $attempt, int $fullLockCount = 0)
    {
        $attempt = $this->update(
            [
                'id' => $attempt->getId(),
                'count' => 0,
                'fullLockCount' => $fullLockCount,
                'trace' => ['reset' => true]
            ],
            [],
            ['simplifiedParams' => true]);

        if ($fullLockCount === 0) {
            $this->lockUser($attempt[0]->getUser(), false);
        }

        return $attempt[0];
    }

    /**
     * Reset User Attempt
     *
     * @param User $user
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetUserAttempt(User $user)
    {
        /** @var LoginAttempt $attempt */
        $attempt = $this->findOneBy(['user'=>$user]);
        if ($attempt) {
            $this->resetAttempt($attempt);
        }
    }

    /**
     * Lock User
     *
     * @param User $user
     * @param bool $lock
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function lockUser(User $user, bool $lock = true)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $user->setLocked($lock);
            $em->persist($user);
            $em->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }
    }

    /**
     * Get Attempt Status
     *
     * @param LoginAttempt $attempt
     * @return int
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getAttemptStatus(LoginAttempt $attempt)
    {
        $current = Carbon::now();
        $max_partial_lock = (int) env('MAX_LOGIN_ATTEMPTS_BEFORE_PARTIAL_LOCK', 0);
        $max_full_lock = (int) env('MAX_LOGIN_ATTEMPTS_BEFORE_FULL_LOCK', 0);
        $partialLockTimeOut = (int) env('LOGIN_PARTIAL_LOCK_TIMEOUT', 0);
        $fullLockTimeOut = (int) env('LOGIN_FULL_LOCK_TIMEOUT', 0);
        $diffSec = $current->diffInSeconds($attempt->getUpdatedAt());

        /** Check for attempts */
        if ($max_partial_lock && $attempt->getCount() >= $max_partial_lock) {
            if ($partialLockTimeOut && $diffSec >= $partialLockTimeOut) {
                $attempt = $this->resetAttempt($attempt, $attempt->getFullLockCount() + 1);
                if ($max_full_lock && $attempt->getFullLockCount() >= $max_full_lock) {
                    return self::LOGIN_ATTEMPT_ERROR_ACCOUNT_FULL_LOCKED;
                } else {
                    return self::LOGIN_ATTEMPT_DEFAULT;
                }
            } else {
                if ($attempt->getCount() >= $max_partial_lock) {
                    $attempt = $this->resetAttempt($attempt, $attempt->getFullLockCount() + 1);
                    if ($max_full_lock && $attempt->getFullLockCount() >= $max_full_lock) {
                        return self::LOGIN_ATTEMPT_ERROR_ACCOUNT_FULL_LOCKED;
                    } else {
                        return self::LOGIN_ATTEMPT_ERROR_ACCOUNT_PARTIAL_LOCKED;
                    }
                } else {
                    return self::LOGIN_ATTEMPT_ERROR_ACCOUNT_PARTIAL_LOCKED;
                }
            }
        } else if ($max_full_lock && $attempt->getFullLockCount() >= $max_full_lock) {
            if ($fullLockTimeOut && $diffSec >= $fullLockTimeOut) {
                $this->resetAttempt($attempt);
                return self::LOGIN_ATTEMPT_DEFAULT;
            } else {
                $this->lockUser($attempt->getUser(), true);
                return self::LOGIN_ATTEMPT_ERROR_ACCOUNT_FULL_LOCKED;
            }
        } else {
            return self::LOGIN_ATTEMPT_DEFAULT;
        }
    }

    /**
     * Attempt Check
     *
     * @param User $user
     * @return int
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function attemptCheck(User $user)
    {
        /** @var LoginAttempt $attempt */
        $attempt = $this->findOneBy(['user'=>$user]);
        return $attempt ? $this->getAttemptStatus($attempt) : self::LOGIN_ATTEMPT_DEFAULT;
    }


    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'read'=>[
                    'permissions'=>[
                        'allowed'=>true
                    ]
                ]
            ],
            'superAdmin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'allowed'=>true
                    ]
                ]
            ],
            // Below here is for testing purposes only
            'testing'=>[]
        ];
    }
}