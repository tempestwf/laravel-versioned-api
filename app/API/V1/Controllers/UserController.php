<?php

namespace App\API\V1\Controllers;

use App\API\V1\Entities\User;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Repositories\RoleRepository;
use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Transformers\UserTransformer;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Dingo\Api\Http\Request;
use Mail;

/** @noinspection LongInheritanceChainInspection */
class UserController extends APIControllerAbstract
{
    /** @var RoleRepository **/
    private $roleRepo;

    /** @var EmailVerificationRepository **/
    private $emailVerificationRepo;

    public function __construct(UserRepository $repo, ToArrayTransformer $arrayTransformer, RoleRepository $roleRepo, EmailVerificationRepository $emailVerificationRepo)
    {
        $this->setRepo($repo);
        $this->setTransformer($arrayTransformer);
        parent::__construct();

        $this->roleRepo = $roleRepo;
        $this->emailVerificationRepo = $emailVerificationRepo;
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
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function onPostStore(SimpleEventContract $event):void
    {
        $result = $event->getEventArgs()['result'];

        /** @var UserRepository $userRepo **/
        $userRepo = $this->getRepo();
        /** @var User $user **/
        $user = $userRepo->findOneBy(['id'=>$result['id']]);

        /** Set user's default role **/
        $this->roleRepo->setUserPermissions($user);

        /** Create the email verification code **/
        $emailVerification = $this->emailVerificationRepo->createEmailVerificationCode($user);

        /**
         * TODO: get this into an email queue
         */
        if ($event->getEventArgs()['frontEndOptions']['email'] === true) {
            Mail::send(
                'emails.activation',
                [
                    'user_name' => $result['name'],
                    'verification_code' => $emailVerification->getVerificationCode(),
                    'email' => $result['email'],
                    'host_name' => env('API_DOMAIN', $_SERVER['HTTP_HOST']),
                    'code' => base64_encode($result['email'] . '_' . $emailVerification->getVerificationCode())
                ],
                function ($m) use ($result) {
                    $m
                        ->from('jerome@sweetspotmotion.com', 'SweetSpotMotion')
                        ->to($result['email'], $result['name'])
                        ->subject('Account Activation');
            });
        }
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate($code)
    {
        if ($code) {
            $verification = explode('_', base64_decode($code));
            /** @var UserRepository $userRepo */
            $userRepo = $this->getRepo();
            $user = $userRepo->findOneBy(['email'=>$verification[0]]);

            /** @var EmailVerification $emailVerification */
            $emailVerification = $user->getEmailVerification();
            if ($emailVerification->getVerified())
            {
                return response()->json(['error' => 'already_activated'], 401);
            }
            else if ($emailVerification->getVerificationCode() === $verification[1])
            {
                $em = app('em');
                $emailVerification->verify(true);
                $em->persist($emailVerification);
                $em->flush();
                $response = ['success' => true, 'message' => 'validated_email_success'];
                return response()->json(compact('response'));
            }
            else
            {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        }
        else
        {
            throw new BadRequestHttpException('verification_code_needed.');
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