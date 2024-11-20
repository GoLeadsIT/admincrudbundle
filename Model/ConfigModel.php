<?php

namespace Goleadsit\AdminCrudBundle\Model;

class ConfigModel {

    private const _ACTIONS = [
        'index', 'new', 'edit', 'show', 'delete'
    ];

    /** @var string */
    private $entity;

    /** @var string */
    private $form;

    /** @var string */
    private $routePrefix;

    /** @var array */
    private $actions;

    /** @var array */
    private $paths;

    /**
     * ConfigModel constructor.
     *
     * @param string $entity
     * @param string $namespace
     * @param string $routePrefix
     * @param array  $actions
     * @param array  $paths
     */
    public function __construct(string $entity, string $form, string $routePrefix, array $actions, array $paths = []) {
        $this->entity = $entity;
        $this->form = $form;
        $this->routePrefix = $routePrefix;
        $this->actions = $actions;
        $this->setPaths($paths);
    }

    /**
     * FQN Entity
     *
     * @return string
     */
    public function getEntity(): string {
        return $this->entity;
    }

    /**
     * @param string $entity
     *
     * @return ConfigModel
     */
    public function setEntity(string $entity): ConfigModel {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Entity Name
     *
     * @return string
     */
    public function getEntityName(): string {
        return substr($this->entity, strrpos($this->entity, '\\') + 1);
    }

    /**
     * @return string
     */
    public function getForm(): string {
        return $this->form;
    }

    /**
     * @param string $form
     *
     * @return ConfigModel
     */
    public function setNamespace(string $form): ConfigModel {
        $this->form = $form;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoutePrefix(): string {
        return $this->routePrefix;
    }

    /**
     * @param string $routePrefix
     *
     * @return ConfigModel
     */
    public function setRoutePrefix(string $routePrefix): ConfigModel {
        $this->routePrefix = $routePrefix;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions(): array {
        return $this->actions;
    }

    /**
     * @param array $actions
     *
     * @return ConfigModel
     */
    public function setActions(array $actions): ConfigModel {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @param string $action
     *
     * @return string|null
     */
    public function getPath(string $action): ?string {
        return $this->paths[$action];
    }

    public function setPaths(array $paths = NULL) {
        foreach(self::_ACTIONS as $key => $action) {
            $this->paths[$action] = (!$this->actions[$action])
                ? $paths[$action]
                : 'admincrud_' . strtolower($this->getEntityName()) . '_' . $action;
        }

        return $this;
    }
}
