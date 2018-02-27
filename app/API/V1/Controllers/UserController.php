<?php

namespace App\API\V1\Controllers;

use App\API\V1\Entities\EmailVerification;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Transformers\UserTransformer;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Dingo\Api\Http\Request;
use Mail;

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

            $verification_code = str_random(env('AUTH_PASSWORD_LENGTH', 30));
            $emailVerification = new EmailVerification();
            $emailVerification->setVerificationCode($verification_code);
            $emailVerification->setUser($user);
            $em->persist($emailVerification);
            $em->flush();

            $user->setEmailVerification($emailVerification);
            $em->persist($user);
            $em->flush();

            Mail::send(
                'emails.activation',
                [
                    'user_name' => $result['name'],
                    'verification_code' => $emailVerification->getVerificationCode(),
                    'email' => $result['email'],
                    'host_name' => env('APP_URL', $_SERVER['HTTP_HOST']),
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