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

use  Buzz\Browser,
     Buzz\Client\Curl;

 /**
  * HTTP client
  *
  * @package NewVisionMailChimpBundle
  * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
  */
class HttpClient
{
    protected $dataCenter;
    protected $apiKey;
    protected $listId;

    /**
     * Initializes Http client
     *
     * @param string $apiKey     mailchimp api key
     * @param string $listId     mailing list id
     * @param string $dataCenter mailchimp datacenter
     */
    public function __construct($apiKey, $listId, $dataCenter)
    {
        $this->apiKey = $apiKey;
        $this->listId = $listId;
        $this->dataCenter = $dataCenter;
    }

    /**
     * Send API request to mailchimp
     *
     * @param string  $apiCall mailchimp method
     * @param string  $payload Request information
     *
     * @return json
     */
    protected function makeRequest($method, $apiCall, $payload)
    {
        $url = $this->dataCenter . '3.0' . $apiCall;

        $response = null;
        $headers = array(
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
            'Authorization' => 'apikey ' . $this->apiKey
        );

        $curl = new Curl();
        $curl->setOption(CURLOPT_USERPWD, 'anystring:'.$this->apiKey);
        $curl->setOption(CURLOPT_TIMEOUT, 600);
        $curl->setOption(CURLOPT_USERAGENT, 'NewVisionMailChimpBundle');
        //$curl->setOption(CURLOPT_RETURNTRANSFER, true);
        //$curl->setOption(CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
        //$curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        //$curl->setOption(CURLOPT_ENCODING, '');
        //$curl->setOption(CURLINFO_HEADER_OUT, true);

        $browser = new Browser($curl);
        switch ($method) {
            case 'get':
                $response = $browser->get($url, $headers);
                break;
            case 'post':
                $encoded = json_encode($payload);
                $curl->setOption(CURLOPT_POSTFIELDS, $encoded);

                $response = $browser->post($url, $headers, http_build_query($payload));
                break;
            case 'delete':
                $response = $browser->delete($url, $headers, http_build_query($payload));
                break;
            case 'put':
                $curl->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
                $encoded = json_encode($payload);
                $curl->setOption(CURLOPT_POSTFIELDS, $encoded);

                $response = $browser->put($url, $headers, http_build_query($payload));
                break;
            case 'patch':
                $encoded = json_encode($payload);

                $response = $browser->patch($url, $headers, http_build_query($payload));
                break;
            default:
                # code...
                break;
        }

        return $response->getContent();
    }

}
