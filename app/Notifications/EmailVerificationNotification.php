<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/16/2018
 * Time: 5:22 PM
 */

namespace TempestTools\Raven\Laravel\Orm\Notification;


use App\API\V1\Entities\User;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationNotification extends GeneralNotificationAbstract
{


    /**
     * A method to be overridden add additional stuff to a mail message (such as the sort of things that generally be handled by a view).
     *
     * @param MailMessage $mailMessage
     * @param User $notifiable
     * @param array $settings
     * @return MailMessage
     */
    protected function addToMailMessage(MailMessage $mailMessage, /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */ User $notifiable, array $settings):MailMessage {
        $verificationKey = $notifiable->getEmailVerification()->getId();
        $mailMessage->greeting('Welcome to TempestTools');
        $mailMessage->action(env('APP_URL') . 'contexts/guest/email-verification/' . $verificationKey, 'Click here to verify your account');
        return $mailMessage;
    }
}