<?php
/**
 * This file is part of the NewVisionMailChimpBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewVision\MailChimpBundle\Services;

/**
 * MailChimp init
 *
 * @package NewVisionMailChimpBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class MailChimp
{

    private $apiKey;
    private $dataCenter;

    /**
     * Initializes MailChimp
     *
     * @param string $apiKey Mailchimp api key
     */
    public function __construct($apiKey, $ssl = true)
    {
        $this->apiKey = $apiKey;

        $key = preg_split("/-/", $this->apiKey);

        if (array_key_exists(1, $key)) {
            if($ssl) {
                $this->dataCenter ='https://' . $key[1] . '.api.mailchimp.com/';
            }else {
                $this->dataCenter ='http://' . $key[1] . '.api.mailchimp.com/';
            }
        }  else {
            throw new \Exception('ApiKey is not valid');
        }

        if (!function_exists('curl_init')) {
            throw new \Exception('This bundle needs the cURL PHP extension.');
        }
    }

    /**
     * Get Mailchimp api key
     *
     * @return string
     */
    public function getAPIkey()
    {
        return $this->apiKey;
    }

    /**
     * get datacenter
     *
     * @return string $datacenter
     */
    public function getDatacenter()
    {
        return $this->dataCenter;
    }

    /**
     * Get List Methods
     *
     * @return Methods\MCList
     */
    public function getList($listId = null)
    {
        return new Methods\MCList($this->apiKey, $listId, $this->dataCenter);
    }
}
