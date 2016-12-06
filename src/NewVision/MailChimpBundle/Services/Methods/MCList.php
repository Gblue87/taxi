<?php
/**
 * This file is part of the NewVisionMailChimpBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewVision\MailChimpBundle\Services\Methods;

use NewVision\MailChimpBundle\Services\HttpClient;

/**
 *  Mail Chimp List method
 *
 * @package NewVisionMailChimpBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
class MCList extends HttpClient
{

    private $merge = array();
    private $emailType = 'html';
    private $doubleOptin = true;
    private $updateExisting = false;
    private $replaceInterests = true;
    private $sendWelcome = false;
    private $email;
    private $mergeVars = array();
    private $deleteMember = true;
    private $sendGoodbye = false;
    private $sendNotify = false;

    /**
     * Set mailchimp merge
     *
     * @param array $merge subscribe merge
     */
    public function setMerge(array $merge)
    {
        $this->merge = $merge;
    }

    /**
     * Set mailchimp email type
     *
     * @param string $emailType string to send email type
     */
    public function setEmailType($emailType)
    {
        $this->emailType = $emailType;
    }

    /**
     * Set mailchimp double optin
     *
     * @deprecated due to spelling mistake
     * @param boolean $doubleOptin boolen to double optin
     */
    public function setDoubleOption($doubleOptin)
    {
        $this->setDoubleOptin($doubleOptin);
    }

    /**
     * Set mailchimp double optin
     *
     * @param boolean $doubleOptin boolen to double optin
     */
    public function setDoubleOptin($doubleOptin)
    {
        $this->doubleOptin = $doubleOptin;
    }

    /**
     * Set mailchimp update existing
     *
     * @param boolean $updateExisting boolean to update user
     */
    public function setUpdateExisting($updateExisting)
    {
        $this->updateExisting = $updateExisting;
    }

    /**
     * Set mailchimp replace interests
     *
     * @param boolean $replaceInterests boolean to replace intersests
     */
    public function setReplaceInterests($replaceInterests)
    {
        $this->replaceInterests = $replaceInterests;
    }

    /**
     * Set mailchimp send welcome
     *
     * @param boolean $sendWelcome boolen to send welcome email
     */
    public function SendWelcome($sendWelcome)
    {
        $this->sendWelcome = $sendWelcome;
    }

    /**
     * Set user email
     *
     * @param string $email user email
     */

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function deleteListMember($subscriberHash)
    {
        $payload = array(
        );

        $apiCall = sprintf('/lists/%s/members/%s', $this->listId, $subscriberHash);
        $data = $this->makeRequest('delete', $apiCall, $payload);
        return json_decode($data);
    }

    public function getAllListMembers()
    {
        $payload = array(
        );

        $apiCall = sprintf('/lists/%s/members', $this->listId);
        $data = $this->makeRequest('get', $apiCall, $payload);
        return json_decode($data);
    }

    public function editListMember($email, $status)
    {
        $payload = array(
            'status' => $status,
            'email_address' => $email
        );

        $apiCall = sprintf('/lists/%s/members/%s', $this->listId, md5(strtolower($email)));
        $data = $this->makeRequest('put', $apiCall, $payload);
        return json_decode($data);
    }

    public function createListMember($email, $status)
    {
        $payload = array(
            'status' => $status,
            'email_address' => $email
        );

        $apiCall = sprintf('/lists/%s/members', $this->listId);
        $data = $this->makeRequest('post', $apiCall, $payload);
        return json_decode($data);
    }

    /**
     * Get all lists
     * @return array
     */
    public function lists()
    {
        $payload = array();
        $apiCall = '/lists';
        $data = $this->makeRequest('get', $apiCall, $payload);
        return json_decode($data);
    }
}
