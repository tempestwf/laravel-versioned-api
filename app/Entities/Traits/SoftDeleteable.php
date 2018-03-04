<?php

namespace App\Entities\Traits;

/**
 * SoftDeletes
 * SoftDeleteable behavior allows to "soft delete" objects, filtering them at SELECT time by marking them as with a timestamp, but not explicitly removing them from the database.
 *
 * * Works with DQL DELETE queries (using a Query Hint).
 * * All SELECT queries will be filtered, not matter from where they are executed (Repositories, DQL SELECT queries, etc).
 * * Can be nested with other behaviors
 * * Annotation, Yaml and Xml mapping support for extensions
 * * Support for 'timeAware' option: When creating an entity set a date of deletion in the future and never worry about cleaning up at expiration time.
 *
 * link : https://www.laraveldoctrine.org/docs/1.0/extensions/softdeletes
 */

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity as SoftDeleteableEntityTraits;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
trait SoftDeleteable
{
    use SoftDeleteableEntityTraits;
}