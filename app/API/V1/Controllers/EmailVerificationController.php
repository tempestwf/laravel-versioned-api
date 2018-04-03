<?php

namespace App\API\V1\Controllers;

use App\API\V1\Repositories\EmailVerificationRepository;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Orm\Transformers\ToArrayTransformer;

/** @noinspection LongInheritanceChainInspection */
class EmailVerificationController extends APIControllerAbstract
{
    public function __construct(EmailVerificationRepository $repo, ToArrayTransformer $arrayTransformer)
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
        return [
            'default'=>[
                'GET'=>[
                    'allowed'=>false,
                    'allowIndex'=>false
                ],
                'POST'=>[
                    'allowed'=>false,
                ],
                'PUT'=>[
                    'allowed'=>false,
                ],
                'DELETE'=>[
                    'allowed'=>false,
                ]
            ],
            'guest'=>[
                'GET'=>[
                    'allowed'=>true,
                    'allowIndex'=>false
                ],
                'POST'=>[
                    'allowed'=>false,
                ],
                'PUT'=>[
                    'allowed'=>true,
                ],
                'DELETE'=>[
                    'allowed'=>false,
                ]
            ],
            'admin'=>[
                'GET'=>[
                    'allowed'=>true,
                    'allowIndex'=>true
                ],
                'POST'=>[
                    'allowed'=>false,
                ],
                'PUT'=>[
                    'allowed'=>true,
                ],
                'DELETE'=>[
                    'allowed'=>true,
                ]
            ],
        ];
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onInit (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreIndex (SimpleEventContract $event):void
    {
        //
    }


    /**
     * @param SimpleEventContract $event
     */
    public function onPostIndex (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreStore (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostStore (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreShow (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostShow (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreUpdate (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostUpdate (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPreDestroy (SimpleEventContract $event):void
    {
        //
    }

    /**
     * @param SimpleEventContract $event
     */
    public function onPostDestroy (SimpleEventContract $event):void
    {
        //
    }

}
