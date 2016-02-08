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

namespace Mailchimp\Form;

use Mailchimp\Mailchimp;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ConfigQuery;

/**
 * Class MailchimpConfigurationForm
 * @package Mailchimp\Form
 * @author Loïc Berthelot <loic.berthelot@blumug.com>
 */
class MailchimpConfigurationForm extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $translator = Translator::getInstance();

        $this->formBuilder
              ->add("newsletter_list_id", "text", array(
                "label" => $translator->trans("Newsletter list ID", [], Mailchimp::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "newsletter_list_id"],
                "required" => true,
                "constraints" => array(
                  new NotBlank(),
                ),
                "data" => ConfigQuery::read(Mailchimp::CONFIG_NEWSLETTER_LIST_ID)
              ))
            ->add("api_key", "text", array(
                "label" => $translator->trans("Api key", [], Mailchimp::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "api_key"],
                "required" => true,
                "constraints" => array(
                    new NotBlank(),
                ),
                "data" => ConfigQuery::read(Mailchimp::CONFIG_API_KEY)
            ))
            ->add("api_url", "text", array(
                "label" => $translator->trans("Api URL", [], Mailchimp::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "api_url"],
                "required" => true,
                "constraints" => array(
                    new NotBlank(),
                ),
                "data" => ConfigQuery::read(Mailchimp::CONFIG_API_URL)
            ))
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "mailchimp_configuration";
    }
}
