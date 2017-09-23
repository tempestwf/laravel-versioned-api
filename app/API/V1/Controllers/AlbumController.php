<?php

namespace App\API\V1\Controllers;

use App\API\V1\Repositories\AlbumRepository;
use TempestTools\Common\Doctrine\Transformers\ToArrayTransformer;
use TempestTools\Crud\Laravel\Controllers\RestfulControllerTrait;

/** @noinspection LongInheritanceChainInspection */
class AlbumController extends APIControllerAbstract
{
    use RestfulControllerTrait;
    public function __construct(AlbumRepository $repo, ToArrayTransformer $arrayTransformer)
    {
        $this->setRepo($repo);
        $this->setTransformer($arrayTransformer);
        parent::__construct();
    }
}
