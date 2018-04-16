<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/16/2018
 * Time: 5:22 PM
 */
namespace App\Notifications;
use App\API\V1\Entities\User;
use Illuminate\Notifications\Messages\MailMessage;
use TempestTools\Scribe\Contracts\Orm\EntityContract;
use TempestTools\Raven\Laravel\Orm\Notification\GeneralNotificationAbstract;

class EmailVerificationNotification extends GeneralNotificationAbstract
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
         * @var User $notifiable
         */
        return $this->addUserEntityToMailMessage($mailMessage, $notifiable, $settings);
    }
    /**
     * Formats the email with info from the user Entity
     * @param MailMessage $mailMessage
     * @param User $notifiable
     * @param array $settings
     * @return MailMessage
     */
    protected function addUserEntityToMailMessage (MailMessage $mailMessage, User $notifiable, array $settings):MailMessage {
        // This is just example code, a real email would take the user to a webpage that would leverage the api to verify the token, and then redirect to a success page.
        $verificationKey = $notifiable->getEmailVerification()->getId();
        $mailMessage->greeting('Welcome to TempestTools');
        $mailMessage->action(env('APP_URL') . 'contexts/guest/email-verification/' . $verificationKey, 'Click here to verify your account');
        return $mailMessage;
    }
}
