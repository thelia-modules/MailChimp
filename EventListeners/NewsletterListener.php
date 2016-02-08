<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Mailchimp\EventListeners;

use Mailchimp\Api\MailChimp;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Newsletter\NewsletterEvent;
use Mailchimp\Mailchimp as MailchimpModule;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;
use Thelia\Model\NewsletterQuery;

/**
 * Class NewsletterListener
 * @package Mailchimp\EventListeners
 * @author Loïc Berthelot <loic.berthelot@blumug.com>
 */
class NewsletterListener implements EventSubscriberInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var MailChimp
     */
    protected $api;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        $this->api = new MailChimp(ConfigQuery::read(MailchimpModule::CONFIG_API_KEY));
    }

    public function subscribe(NewsletterEvent $event)
    {
        $list_id = ConfigQuery::read(MailchimpModule::CONFIG_NEWSLETTER_LIST_ID);
        $email = $event->getEmail();

        $result = $this->api->post("lists/$list_id/members", [
          'email_address' => $email,
          'status'        => 'subscribed',
        ]);

        TLog::getInstance()->addDebug("Mailchimp subscribe result ".json_encode($result));

        $status = $result["status"];

        if (!strcmp($status, "subscribed")) {
            $status = 200;
        }

        $errorMessage = "An error occurred during the newsletter registration process";
        if ($status >= 300) {

            $title = $result["title"];
            TLog::getInstance()->addError("Mailchimp subscribe error ".$status." : ".$title);

            if (!strcmp($title, "Member Exists")) {

                $errorMessage = "Your email address has already been registered.";
            }
            else {
                TLog::getInstance()->addError("Mailchimp subscribe getLastError ".$this->api->getLastError());
                $string = json_encode($this->api->getLastResponse());
                TLog::getInstance()->addDebug("Mailchimp subscribe getLastResponse ". $string);
                $string = json_encode($this->api->getLastRequest());
                TLog::getInstance()->addDebug("Mailchimp subscribe getLastRequest ". $string);
            }
        }


        $this->logAfterAction(

          $this->translator->trans(
            "You have successfully registered to the newsletter",
            [],
            MailchimpModule::MESSAGE_DOMAIN
          ),

          $this->translator->trans(
            $errorMessage,
            [],
            MailchimpModule::MESSAGE_DOMAIN
          ),

          $status
        );


    }

    protected function logAfterAction($successMessage, $errorMessage, $status)
    {
        if ($status >= 200 && $status < 300) {
            Tlog::getInstance()->info($successMessage);
        } else {
            Tlog::getInstance()->error(sprintf("%s. Status code: %d", $errorMessage, $status));
            throw new \InvalidArgumentException($errorMessage);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::NEWSLETTER_SUBSCRIBE => array("subscribe", 192)
        );
    }
}
