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

namespace Mailchimp\Controller;

use Mailchimp\Form\MailchimpConfigurationForm;
use Mailchimp\Mailchimp;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

/**
 * Class MailchimpConfigController
 * @package Mailchimp\Controller
 * @author Loïc Berthelot <loic.berthelot@blumug.com>
 */
class MailchimpConfigController extends BaseAdminController
{
    public function showAction()
    {
        return $this->render("mailchimp-configuration");
    }

    public function saveAction()
    {
        $baseForm = new MailchimpConfigurationForm($this->getRequest());

        try {
            $form = $this->validateForm($baseForm);
            $data = $form->getData();

            ConfigQuery::write(Mailchimp::CONFIG_NEWSLETTER_LIST_ID, $data["newsletter_list_id"]);
            ConfigQuery::write(Mailchimp::CONFIG_API_KEY, $data["api_key"]);
            ConfigQuery::write(Mailchimp::CONFIG_API_URL, $data["api_url"]);

            $this->getParserContext()->set("success", true);
        } catch (\Exception $e) {
            $this->getParserContext()
                ->setGeneralError($e->getMessage())
                ->addForm($baseForm)
            ;
        }

        if ("close" === $this->getRequest()->request->get("save_mode")) {
            return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/modules"));
        }

        return $this->showAction();
    }
}
