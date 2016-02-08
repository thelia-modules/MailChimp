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

namespace Mailchimp;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\Config;
use Thelia\Model\ConfigQuery;
use Thelia\Module\BaseModule;

/**
 * Class Mailchimp
 * @package Mailchimp
 * @author Loïc Berthelot <loic.berthelot@blumug.com>
 */
class Mailchimp extends BaseModule
{
    const MESSAGE_DOMAIN = "mailchimp";

    const CONFIG_NEWSLETTER_LIST_ID = "mailchimp.newsletter_list_id";
    const CONFIG_API_KEY = "mailchimp.api.key";
    const CONFIG_API_URL = "mailchimp.api.url";

    public function postActivation(ConnectionInterface $con = null)
    {
        $con->beginTransaction();

        try {

            if (null === ConfigQuery::read(static::CONFIG_NEWSLETTER_LIST_ID)) {
                $this->createConfigValue(static::CONFIG_NEWSLETTER_LIST_ID, [
                  "fr_FR" => "ID de votre liste de diffusion Mailchimp",
                  "en_US" => "Mailchimp diffusion list ID",
                ]);
            }

            if (null === ConfigQuery::read(static::CONFIG_API_KEY)) {
                $this->createConfigValue(static::CONFIG_API_KEY, [
                    "fr_FR" => "Clé d'API pour Mailchimp",
                    "en_US" => "Api key for Mailchimp",
                ]);
            }

            if (null === ConfigQuery::read(static::CONFIG_API_URL)) {
                $this->createConfigValue(static::CONFIG_API_URL, [
                  "fr_FR" => "URL de l'API Mailchimp",
                  "en_US" => "Mailchimp API URL",
                ]);
            }

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();

            throw $e;
        }
    }

    protected function createConfigValue($name, array $translation, $value = '')
    {
        $config = new Config();
        $config
            ->setName($name)
            ->setValue($value)
        ;

        foreach ($translation as $locale => $title) {
            $config->getTranslation($locale)
                ->setTitle($title)
            ;
        }

        $config->save();
    }
}
