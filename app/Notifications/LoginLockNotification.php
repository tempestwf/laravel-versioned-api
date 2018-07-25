<?php
/**
 * Created by PhpStorm.
 * User: monxe
 * Date: 17/07/2018
 * Time: 10:33 PM
 */

namespace App\Notifications;
use App\API\V1\Entities\LoginAttempt;
use App\API\V1\Entities\PasswordReset;
use Illuminate\Notifications\Messages\MailMessage;
use TempestTools\Scribe\Contracts\Orm\EntityContract;
use TempestTools\Raven\Laravel\Notifications\GeneralNotificationAbstract;

class LoginLockNotification extends GeneralNotificationAbstract
{
    /**
     * A method to be overridden add additional stuff to a mail message (such as the sort of things that generally be handled by a view).
     *
     * @param MailMessage $mailMessage
     * @param EntityContract $notifiable
     * @param array $settings
     * @return MailMessage
     */
    protected function addToMailMessage(MailMessage $mailMessage, EntityContract $notifiable, array $settings):MailMessage {
        /**
         * @var PasswordReset $notifiable
         */
        return $this->addUserEntityToMailMessage($mailMessage, $notifiable, $settings);
    }
    /**
     * Formats the email with info from the user Entity
     * @param MailMessage $mailMessage
     * @param LoginAttempt $notifiable
     * @param array $settings
     * @return MailMessage
     */
    protected function addUserEntityToMailMessage (MailMessage $mailMessage, LoginAttempt $notifiable, array $settings):MailMessage {
        $max_full_lock = (int) env('MAX_LOGIN_ATTEMPTS_BEFORE_FULL_LOCK', 0);

        $mailMessage->greeting('Hello ' . $notifiable->getUser()->getName() . ',');
        if ($notifiable->getFullLockCount() < $max_full_lock) {
            $mailMessage->subject(trans('email.auth_partial_lock_subject'));
            $mailMessage->line(trans('email.auth_partial_lock_line_1'));
            $mailMessage->line(trans('email.auth_partial_lock_line_2'));
        } else {
            $mailMessage->subject(trans('email.auth_full_lock_subject'));
            $mailMessage->line(trans('email.auth_full_lock_line_1'));
            $mailMessage->line(trans('email.auth_full_lock_line_2'));
            $mailMessage->action(trans('email.auth_full_lock_action'), env('APP_URL') . 'contexts/guest/reactivate/' . $notifiable->getUser()->getId());
        }

        return $mailMessage;
    }
}