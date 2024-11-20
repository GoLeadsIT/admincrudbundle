<?php

namespace Goleadsit\AdminCrudBundle\DependencyInjection;

use Goleadsit\AdminCrudBundle\Model\ConfigModel;

class ConfigManager {

    /** @var \Goleadsit\AdminCrudBundle\Model\ConfigModel[] */
    private $config;

    /**
     * ConfigManager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = [];

        foreach($config as $key => $value) {
            $this->config[$key] = new ConfigModel($key, $value['form'], $value['route'], $value['actions'], $value['paths']);
        }
    }

    /**
     * @return \Goleadsit\AdminCrudBundle\Model\ConfigModel[]
     */
    public function getConfig(): array {
        return $this->config;
    }

}
