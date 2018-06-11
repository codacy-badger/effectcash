<?php
  namespace OCA\EffectCash\Service;

  #use \OCA\Zenodo\Controller\SettingsController;

  use OCP\IConfig;

  class ConfigService {
  	private $appDefaults = [
  	];
    private $userDefaults = [
      'dateformat' => 'dd.mm.yyyy'
    ];

  	private $appName;
  	private $config;
    private $userId;

  	public function __construct($appName, IConfig $config, $userId) {
  		$this->appName = $appName;
  		$this->config = $config;
  		$this->userId = $userId;
  	}

    public function userSettings() {
      return [
        'dateformats' => [
          'dd.mm.yyyy' => ['datepicker' => 'dd.mm.yy', 'php' => 'd.m.Y', 'moment' => 'DD.MM.YYYY'],
          'dd/mm/yyyy' => ['datepicker' => 'dd/mm/yy', 'php' => 'd/m/Y', 'moment' => 'DD/MM/YYYY'],
          'mm/dd/yyyy' => ['datepicker' => 'mm/dd/yy', 'php' => 'm/d/Y', 'moment' => 'MM/DD/YYYY'],
          'yyyy-mm-dd' => ['datepicker' => 'yy-mm-dd', 'php' => 'Y-m-d', 'moment' => 'YYYY-MM-DD'],
          'yyyy/mm/dd' => ['datepicker' => 'yy/mm/dd', 'php' => 'Y/m/d', 'moment' => 'YYYY/MM/DD']
        ],
        'dateformat' => $this->getUserValue('dateformat')
      ];
    }

    public function getDateformatPHP() {
      return $this->userSettings()['dateformats'][$this->getUserValue('dateformat')]['php'];
    }

  	/**
  	 * @param string $key
  	 *
  	 * @return string
     */
  	public function getAppValue($key) {
  		$defaultValue = null;
  		if (array_key_exists($key, $this->appDefaults)) {
  			$defaultValue = $this->appDefaults[$key];
  		}
  		return $this->config->getAppValue($this->appName, $key, $defaultValue);
  	}

  	/**
  	 * @param string $key
  	 * @param string $value
  	 *
  	 * @return string
  	 */
  	public function setAppValue($key, $value) {
  		return $this->config->setAppValue($this->appName, $key, $value);
  	}

  	/**
  	 * @param string $key
  	 *
  	 * @return string
  	 */
  	public function deleteAppValue($key) {
  		return $this->config->deleteAppValue($this->appName, $key);
  	}

  	/**
  	 * @param string $key
  	 *
  	 * @return string
  	 */
  	public function getUserValue($key) {
      $defaultValue = null;
      if (array_key_exists($key, $this->userDefaults)) {
        $defaultValue = $this->userDefaults[$key];
      }
  		return $this->config->getUserValue($this->userId, $this->appName, $key, $defaultValue);
  	}

  	/**
  	 * @param string $key
  	 * @param string $value
  	 *
  	 * @return string
  	 */
  	public function setUserValue($key, $value) {
  		return $this->config->setUserValue($this->userId, $this->appName, $key, $value);
  	}

  	/**
  	 * @param string $userId
  	 * @param string $key
  	 *
  	 * @return string
  	 */
  	public function getValueForUser($userId, $key) {
  		return $this->config->getUserValue($userId, $this->appName, $key);
  	}

  	/**
  	 * @param string $userId
  	 * @param string $key
  	 * @param string $value
  	 *
  	 * @return string
  	 */
  	public function setValueForUser($userId, $key, $value) {
  		return $this->config->setUserValue($userId, $this->appName, $key, $value);
  	}

  	/**
  	 * @param boolean $complete
  	 *
  	 * @return string|integer
  	 */
  	public function getCloudVersion($complete = false) {
  		$ver = \OCP\Util::getVersion();
  		if ($complete) {
  			return implode('.', $ver);
  		}
  		return $ver[0];
  	}
  }
