<?php

namespace App\API\V1\Controllers;

use App\API\V1\Entities\EmailVerification;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Transformers\UserTransformer;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;
use Dingo\Api\Http\Request;
use Mail, Hash;

/** @noinspection LongInheritanceChainInspection */
class UserController extends APIControllerAbstract
{

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
     * @param SimpleEventContract $event
     */
    public function onPostStore (SimpleEventContract $event):void
    {
        if ($event->getEventArgs()['frontEndOptions']['email'] === true) {
            $result = $event->getEventArgs()['result'];
            $query = $event->getEventArgs()['params'];

            $em = app('em');

            /** @var UserRepository $userRepo */
            $userRepo = $this->getRepo();
            $user = $userRepo->findOneBy(['id'=>$result['id']]);

            $verification_code = str_random(30);
            $emailVerification = new EmailVerification();
            $emailVerification->setVerificationCode($verification_code);
            $emailVerification->setUser($user->id);
            $em->persist($emailVerification);
            $em->flush();

            $user->setEmailVerification($emailVerification->getId());
            $em->persist($user);
            $em->flush();

            var_dump($user); die;

            $emailData = [
                'user_name' => $result['name'],
                'verification_code' => $verification_code
            ];

            Mail::send('emails.activation', $emailData, function ($m) use ($result) {
                $m
                    ->from('jerome@sweetspotmovement.com', 'Jerome Erazo')
                    ->to($result['email'], $result['name'])
                    ->subject('Account Activation');
            });
        }
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