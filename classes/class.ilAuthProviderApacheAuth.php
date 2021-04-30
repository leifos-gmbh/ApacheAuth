<?php

class ilAuthProviderApacheAuth extends ilAuthProvider implements ilAuthProviderInterface
{
    /**
     * @var ilLogger|null
     */
    private $logger = null;

    /**
     * @var ilApacheAuthPluginSettings|null
     */
    private $server = null;

    /**
     * ilAuthProviderApacheAuth constructor.
     * @param ilAuthCredentials $credentials
     */
    public function __construct(ilAuthCredentials $credentials)
    {
        global $DIC;

        $this->logger = $DIC->logger()->auth();
        $this->server = ilApacheAuthPluginSettings::getInstance();

        parent::__construct($credentials);
    }

    /**
     * @return ilApacheAuthPluginSettings
     */
    public function getServer() : ilApacheAuthPluginSettings
    {
        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function doAuthentication(ilAuthStatus $status)
    {
        if (!isset($_SERVER[$this->getServer()->getIndicatorName()])) {
            $this->logger->debug('Authentication failed: no input for indicator value found');
            $status->setStatus(ilAuthStatus::STATUS_AUTHENTICATION_FAILED);
            $status->setReason('err_wrong_login');
            return false;
        }
        if (strcasecmp($_SERVER[$this->getServer()->getIndicatorName()], $this->getServer()->getIndicatorValue()) !== 0) {
            $this->logger->debug($this->getServer()->getIndicatorValue() . ' does not match ' . $_SERVER[$this->getServer()->getIndicatorName()]);
            $status->setStatus(ilAuthStatus::STATUS_AUTHENTICATION_FAILED);
            $status->setReason('err_wrong_login');
            return false;
        }

        $uname = $_SERVER[$this->getServer()->getUsernameField()];
        $this->logger->info('Original username: ' . $uname);
        if (!strlen($uname)) {
            $this->logger->info('No username given');
            $status->setStatus(ilAuthStatus::STATUS_AUTHENTICATION_FAILED);
            $status->setReason('err_wrong_login');
            return false;
        }
        if (!str_contains($uname, '@')) {
            $uname_without_domain = $uname;
        } else {
            $uname_without_domain = substr($uname, 0, strpos($uname, '@'));
        }
        $this->logger->info('Shortened username: ' . $uname_without_domain);

        // check for external account
        $servers = ilLDAPServer::_getActiveServerList();
        foreach ($servers as $server_id) {
            $ext_account = ilObjUser::_checkExternalAuthAccount('ldap_' . $server_id, $uname_without_domain);
            if ($ext_account) {
                $this->logger->info('Found ILIAS login name "' . $ext_account . '" for user: ' . $uname_without_domain);
                $status->setStatus(ilAuthStatus::STATUS_AUTHENTICATED);
                $status->setAuthenticatedUserId(ilObjUser::_lookupId($ext_account));
                return true;
            }
        }
        $status->setStatus(ilAuthStatus::STATUS_AUTHENTICATION_FAILED);
        $status->setReason('err_wrong_login');
        return false;
    }
}
