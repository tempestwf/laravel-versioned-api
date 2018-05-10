<?php

namespace App\Exceptions;

/**
 * Exception related tempest tools configurations
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class EmailVerificationException extends \RunTimeException
{
    /**
     * @return EmailVerificationException
     */
    public static function tokenExpired (): EmailVerificationException
    {
        return new self (sprintf('Error: Email verification token expired.'));
    }
}



