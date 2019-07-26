<?php
/**
 * Created by PhpStorm.
 * User: monxe
 * Date: 18/09/2018
 * Time: 1:21 PM
 */

namespace App\Entities\Traits;


trait GenerateRandomString
{
    public function generateRandomString(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }
}