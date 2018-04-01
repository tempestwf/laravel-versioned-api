<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use App\API\V1\Entities\SocializeUser;
use App\Repositories\Repository;
use Doctrine\ORM\Query\Expr;
use TempestTools\Moat\Contracts\RepoHasPermissionsContract;
use TempestTools\Moat\Repository\HasPermissionsQueryTrait;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use Laravel\Socialite\Two\User as SocialiteUser;
use Hash;

/** @noinspection LongInheritanceChainInspection */
class UserRepository extends Repository implements RepoHasPermissionsContract
{
    use HasPermissionsQueryTrait;

	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = User::class;

    /**
     * @param string $socializeType
     * @param SocialiteUser $socialiteUser
     * @return User|null
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function registerSocializeUser(string $socializeType, SocialiteUser $socialiteUser):User
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        $user = null;
        try {
            $user = new User();
            $user->setName($socialiteUser->name);
            $user->setEmail($socialiteUser->email);
            $user->setLocale('en');
            $user->setPassword(Hash::make($socialiteUser->token));

            $socializeUser = new SocializeUser();
            $socializeUser->setUser($user);
            $socializeUser->setAvatar($socialiteUser->avatar);
            // TODO: This seems to be broken @jerome
            $socializeUser->setAvatarOriginal($socialiteUser->avatar_original);
            $socializeUser->setExpiresIn($socialiteUser->expiresIn);
            $socializeUser->setNickname($socialiteUser->nickname);
            if (strtolower($socializeType) === 'facebook') {
                // TODO: This seems to be broken @jerome
                $socializeUser->setProfileUrl($socialiteUser->profileUrl);
            }
            $socializeUser->setRefreshToken($socialiteUser->refreshToken);
            $socializeUser->setSocializeId($socialiteUser->id);
            $socializeUser->setToken($socialiteUser->token);
            $socializeUser->setType($socializeType);

            $em->persist($user);
            $em->persist($socializeUser);
            $em->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }

        return $user;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getTTConfig(): array
    {
        $expr = new Expr();
        /** @noinspection NullPointerExceptionInspection */
        return [
            // Default context that most requests fall back too, and other contexts inherit from.
            'default'=>[
                'read'=>[
                    'query'=>[
                        // Get just the id, name, address, job fields for read actions default.
                        'select'=>[
                            'standardSelect'=>'partial u.{id, name, address, job, locale, createdAt, updatedAt}'
                        ],
                        'where'=>[
                            // Only retrieve data about the currently logged in user by default.
                            'onlyCurrentUser'=>[
                                'type'=>'and',
                                'value'=>$expr->eq('u.id', $this->getArrayHelper()->parseArrayPath([CommonArrayObjectKeyConstants::USER_KEY_NAME, 'id']))
                            ]
                        ],
                    ],
                    'permissions'=>[
                        'where'=>[
                            'fields'=>[
                                'password'=>[
                                    'permissive'=>false
                                ]
                            ]
                        ]
                    ]
                ],

            ],
            'user'=>[
                'extends'=>[':default']
            ],
            'admin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'query'=>[
                        // When in admin context, no longer only return info about the currently logged in user.
                        'where'=>[
                            'onlyCurrentUser' => null
                        ],
                    ],
                ],
            ],
            // Below here is for testing purposes only
            'testing'=>[]
        ];
    }
}