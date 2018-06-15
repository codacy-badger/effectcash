<?php
  namespace OCA\EffectCash\Service;

  #use \OCA\Zenodo\Controller\SettingsController;

  use OCP\IConfig;

  class ConfigService {

    private $appName;
  	private $config;
    private $userId;

    private $defaults = [
      'dateformat' => 'dd.mm.yyyy',
      'currency' => 'euro'
    ];

  	public function __construct($appName, IConfig $config, $userId) {
  		$this->appName = $appName;
  		$this->config = $config;
  		$this->userId = $userId;
  	}

    /**
  	 * @return array
  	 */
    public function getSettingsContainer() {
      return [
        'settings' => [
          'dateformat' => $this->getValue('dateformat'),
          'currency' => $this->getValue('currency')
        ],
        'settings_defaults' => [
          'dateformat' => [
            'dd.mm.yyyy' => ['preview' => date('d.m.Y'), 'datepicker' => 'dd.mm.yy', 'php' => 'd.m.Y', 'moment' => 'DD.MM.YYYY'],
            'dd/mm/yyyy' => ['preview' => date('d/m/Y'), 'datepicker' => 'dd/mm/yy', 'php' => 'd/m/Y', 'moment' => 'DD/MM/YYYY'],
            'mm/dd/yyyy' => ['preview' => date('m/d/Y'), 'datepicker' => 'mm/dd/yy', 'php' => 'm/d/Y', 'moment' => 'MM/DD/YYYY'],
            'yyyy/mm/dd' => ['preview' => date('Y/m/d'), 'datepicker' => 'yy/mm/dd', 'php' => 'Y/m/d', 'moment' => 'YYYY/MM/DD'],
            'yyyy-mm-dd' => ['preview' => date('Y-m-d'), 'datepicker' => 'yy-mm-dd', 'php' => 'Y-m-d', 'moment' => 'YYYY-MM-DD']
          ],
          'currency' => [
            'euro' => ['preview' => '1.234,50 €', 'places' => 2, 'symbol' => '€', 'symbol_ahead' => false, 'thousand' => '.', 'decimal' => ','],
            'us-dollar' => ['preview' => '1,234.50 $', 'places' => 2, 'symbol' => '$', 'symbol_ahead' => false, 'thousand' => ',', 'decimal' => '.']
          ]
        ]
      ];
    }

    /**
  	 * @param array $settings
  	 *
  	 * @return null
  	 */
    public function setSettings($settings) {
      foreach($settings as $key => $value) {
        $this->setValue($key, $value);
      }
      return null;
    }

  	/**
  	 * @param string $key
  	 *
  	 * @return string
  	 */
  	public function getValue($key) {
      $defaultValue = null;
      if (array_key_exists($key, $this->defaults)) {
        $defaultValue = $this->defaults[$key];
      }
  		return $this->config->getUserValue($this->userId, $this->appName, $key, $defaultValue);
  	}

  	/**
  	 * @param string $key
  	 * @param string $value
  	 *
  	 * @return string
  	 */
  	public function setValue($key, $value) {
      return $this->config->setUserValue($this->userId, $this->appName, $key, $value);
  	}

  }
