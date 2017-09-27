<?php

namespace App\API\V1\Controllers;

use App\API\V1\Repositories\AlbumRepository;
use TempestTools\Crud\Laravel\Controllers\RestfulControllerTrait;
use TempestTools\Crud\Orm\Transformers\ToArrayTransformer;

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
