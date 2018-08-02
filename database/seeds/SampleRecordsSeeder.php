<?php

use App\API\V1\Entities\User;
Use App\API\V1\Entities\Artist;
Use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Entities\Album;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Database\Seeder;

class SampleRecordsSeeder extends Seeder
{
    /** @var EntityManagerInterface $em */
    protected $em;

    /**
     * DatabaseSeeder constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function run()
    {
        $conn = $this->em->getConnection();
        try {
            $artistRepo = new ArtistRepository();
            $artistRepo->findOneBy(['name' => 'Brahms']);
            if (!$artistRepo) {
                $conn->beginTransaction();
                $userRepo =  $this->em->getRepository(User::class);
                $user = $userRepo->findOneBy(['id'=>1]);
                $artist = new Artist();
                $artist->setName('Brahms');

                $album = new Album();
                $album->setName('Brahms: Complete Edition');
                $album->setReleaseDate(new DateTime('1995-02-01 00:00:00'));
                $album->addUser($user);

                $artist->addAlbum($album);
                $this->em->persist($artist);
                $this->em->persist($album);
                $this->em->persist($user);
                $this->em->flush();
                $conn->commit();
            }
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}