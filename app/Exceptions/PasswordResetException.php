<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 8/19/2017
 * Time: 6:52 PM
 */

namespace TempestTools\Common\Exceptions\Utility;

/**
 * Exception related tempest tools configurations
 *
 * @link    https://github.com/tempestwf
 * @author  William Tempest Wright Ferrer <https://github.com/tempestwf>
 */
class PasswordResetException extends \RunTimeException
{
    /**
     * @return PasswordResetException
     */
    public static function noPassword (): PasswordResetException
    {
        return new self (sprintf('Error: No new password found in options passed from the front end'));
    }

}



