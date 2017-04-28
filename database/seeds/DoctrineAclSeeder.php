<?php

use Illuminate\Database\Seeder;

class DoctrineAclSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        $rawSql = '
            // TODO: Put inserts here
        ';
        $this->em->getConnection()->exec($rawSql);
        $this->command->comment('Seeded Doctrine ACL Fixture Data');
    }
}
