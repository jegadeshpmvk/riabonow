<?php

/**
 * @copyright Copyright &copy; Gogodigital Srls
 * @company Gogodigital Srls - Wide ICT Solutions
 * @website http://www.gogodigital.it
 * @github https://github.com/cinghie/yii2-elesticemail
 * @license BSD-3-Clause
 * @package yii2-elesticemail
 * @version 0.1.2
 */

namespace cinghie\elasticemail\components;

use ElasticEmailApi\Account;
use ElasticEmailApi\AccessToken;
use ElasticEmailApi\Campaign;
use ElasticEmailApi\Channel;
use ElasticEmailApi\Contact;
use ElasticEmailApi\Domain;
use ElasticEmailApi\EEList;
use ElasticEmailApi\Template;
use ElasticEmailClient\ApiConfiguration;
use ElasticEmailClient\ElasticClient;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class Elasticemail
 *
 * @property ElasticEmailClient $client
 * @property Account $account
 * @property Contact $contacts
 * @property AccessToken $accessTokens
 * @property Campaign $campaigns
 * @property Channel $channels
 * @property Domain $domains
 * @property EEList $eelists
 * @property Template $templates
 *
 * @see https://api.elasticemail.com/public/help
 */
class Elasticemail extends Component
{
	/**
	 * @var string
	 */
	public $apiKey;

	/**
	 * @var string
	 */
	public $apiUrl;

	/**
	 * @var ElasticEmailClient
	 */
	private $_elasticemail;

	/**
	 * Elasticemail constructor
	 *
	 * @param array $config
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct(array $config = [])
	{
		if(!isset($config['apiKey']) || !$config['apiKey']) {
			throw new InvalidConfigException(Yii::t('elasticemail', 'ElasticEmail API Key missing!'));
		}

		$this->apiKey = $config['apiKey'];
		$this->apiUrl = isset($config['apiUrl']) ? $config['apiUrl'] : 'https://api.elasticemail.com/v2/';

		parent::__construct($config);
	}

	/**
	 * Elasticemail init
	 */
	public function init()
	{
		$configuration = new ApiConfiguration([
			'apiUrl' => $this->apiUrl,
			'apiKey' => $this->apiKey
		]);

		$this->_elasticemail = new ElasticClient($configuration);

		parent::init();
	}

	/**
	 * @return ElasticEmailClient
	 */
	public function getClient()
	{
		return $this->_elasticemail;
	}

	/**
	 * @return AccessToken
	 */
	public function getAccessTokens()
	{
		return $this->_elasticemail->AccessToken->EEList();
	}
	/**
	 * @return Account
	 */
	public function getAccount()
	{
		return $this->_elasticemail->Account->Load();
	}

	/**
	 * @return Campaign
	 */
	public function getCampaigns()
	{
		return $this->_elasticemail->Campaign->EEList();
	}

	/**
	 * @return Channel
	 */
	public function getChannels()
	{
		return $this->_elasticemail->Channel->EEList();
	}

	/**
	 * @return Contact
	 */
	public function getContacts()
	{
		return $this->_elasticemail->Contact->EEList();
	}

	/**
	 * @return Domain
	 */
	public function getDomains()
	{
		return $this->_elasticemail->Domain->EEList();
	}

	/**
	 * @return EEList
	 */
	public function getEelists()
	{
		return $this->_elasticemail->EEList->EEList();
	}

	/**
	 * @return Template
	 */
	public function getTemplates()
	{
		return $this->_elasticemail->Template->GetList();
	}
}
