<?php
/**
 * Created by PhpStorm.
 * User: monxe
 * Date: 20/09/2018
 * Time: 7:20 PM
 */

namespace App\API\V1\UnitTest;

use App\API\V1\Entities\User;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;
use TempestTools\Common\Helper\ArrayHelper;

class CrudTestBase extends CrudTestBaseAbstract
{
    public function createRobAndBobData():array
    {
        return [
            [
                'identificationKey' => "testbob",
                'email' => "bob.bobo@bobxx.com",
                'firstName'=>'bob',
                'middleInitial'=>'b',
                'lastName'=>'bobo',
                'age' => 32,
                'gender' => 1,
                'weight' => 210,
                'height' => 180.34,
                'phoneNumber' => "+1 757-571-2711",
                'lifestyle' => 1,
                'password' => "Bobsyouruncle00!",
                'locale' => "en"
            ],
            [
                'identificationKey' => "testrob",
                'email' => "rob.robo@robxx.com",
                'firstName'=>'rob',
                'middleInitial'=>'b',
                'lastName'=>'robo',
                'age' => 32,
                'gender' => 1,
                'weight' => 210,
                'height' => 180.34,
                'phoneNumber' => "+1 757-571-2711",
                'lifestyle' => 1,
                'password' => "Norobsyouruncle00!",
                'locale' => "en"
            ],
        ];
    }

    /**
     * @return array
     */
    public function createData(): array
    {
        $userRepo = $this->em->getRepository(User::class);
        $testUser = $userRepo->findOneBy(['email' => env('BASE_USER_EMAIL')]);
        return [
            [
                'name'=>'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                'releaseDate'=>new \DateTime('now'),
                'artist'=>[
                    'create'=>[
                        [
                            'name'=>'BEETHOVEN',
                            'assignType'=>'set',
                        ],
                    ],
                ],
                'users'=>[
                    'read'=>[
                        $testUser->getId() => [
                            'assignType'=>'addSingle',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return ArrayHelper
     * @throws \TempestTools\Common\Exceptions\Helper\ArrayHelperException
     */
    public function makeArrayHelper ():ArrayHelper {
        /** @var User $repo */
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(['email'=> env('BASE_USER_EMAIL')]);
        $arrayHelper = new ArrayHelper();
        $arrayHelper->extract([$user]);
        return $arrayHelper;
    }
}