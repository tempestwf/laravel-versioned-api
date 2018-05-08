<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 8/19/2017
 * Time: 6:52 PM
 */

namespace App\Exceptions;

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

    /**
     * @return PasswordResetException
     */
    public static function noRole (): PasswordResetException
    {
        return new self (sprintf('Error: You need to have role first before you can update your password.'));
    }

    /**
     * @return PasswordResetException
     */
    public static function emailNotVerified (): PasswordResetException
    {
        return new self (sprintf('Error: You need to verify your email first before resetting password.'));
    }

    /**
     * @return PasswordResetException
     */
    public static function noEmail (): PasswordResetException
    {
        return new self (sprintf('Error: No email in the submitted options.'));
    }

    /**
     * @return PasswordResetException
     */
    public static function noUserAssociatedEmail (): PasswordResetException
    {
        return new self (sprintf('Error: No user is associated with thee given email.'));
    }

    /**
     * @return PasswordResetException
     */
    public static function alreadyVerified (): PasswordResetException
    {
        return new self (sprintf('Error: This password reset token is already verified'));
    }

    /**
     * @return PasswordResetException
     */
    public static function cantSetFalse (): PasswordResetException
    {
        return new self (sprintf('Error: A verification token can not be set to false'));
    }
}



