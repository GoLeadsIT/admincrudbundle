<?php

namespace Goleadsit\AdminCrudBundle\Routing;

use Goleadsit\AdminCrudBundle\DependencyInjection\ConfigManager;
use Goleadsit\AdminCrudBundle\Model\ConfigModel;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AdminCrudLoader extends Loader {

    /** @var \Goleadsit\AdminCrudBundle\DependencyInjection\ConfigManager */
    private $configManager;

    private $isLoaded = false;

    public function __construct(ConfigManager $configManager) {
        $this->configManager = $configManager;
    }

    public function load($resource, $type = NULL) {
        if(true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "admincrud" loader twice');
        }

        $routes = new RouteCollection();

        foreach($this->configManager->getConfig() as $adminCrudConfigModel) {
            foreach($adminCrudConfigModel->getActions() as $action => $value) {
                if($value) {
                    $path = $this->getPath($action, $adminCrudConfigModel->getRoutePrefix());
                    $defaults = $this->getDefaults($action, $adminCrudConfigModel);
                    $route = new Route($path, $defaults);
                    $route->setMethods($this->getMethods($action));
                    $routes->add($adminCrudConfigModel->getPath($action), $route);
                }
            }
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, $type = NULL) {
        return 'extra' === $type;
    }

    /**
     * @param string $action
     * @param string $route
     *
     * @return string Ruta dependiendo de la acción
     */
    private function getPath(string $action, string $route): string {
        if($action !== 'index') {
            if($action === 'new') {
                return '/' . $route . '/' . $action;
            }

            return '/' . $route . '/' . $action . '/{id}';
        }

        return '/' . $route;
    }

    /**
     * @param string $action
     *
     * @return array Métodos asociados a una acción
     */
    private function getMethods(string $action): array {
        if($action === 'new' || $action == 'edit') {
            return ['GET', 'POST'];
        }
        else if($action === 'delete') {
            return ['GET', 'DELETE'];
        }

        return ['GET'];
    }

    /**
     * Dependiendo de la acción devuelve un array con los parametros necesarios
     * para el controlador
     *
     * @param string                                       $action
     * @param \Goleadsit\AdminCrudBundle\Model\ConfigModel $adminCrudConfigModel
     *
     * @return array
     */
    private function getDefaults(string $action, ConfigModel $adminCrudConfigModel): array {
        $defaults = [
            '_controller' => 'Goleadsit\AdminCrudBundle\Controller\CrudController::' . $action,
            'entityFQN'   => $adminCrudConfigModel->getEntity()
        ];

        if($action === 'index') {
            $defaults += [
                'paths' => [
                    'new'    => $adminCrudConfigModel->getPath('new'),
                    'show'   => $adminCrudConfigModel->getPath('show'),
                    'edit'   => $adminCrudConfigModel->getPath('edit'),
                    'delete' => $adminCrudConfigModel->getPath('delete')
                ]
            ];
        }
        else if($action === 'new') {
            $defaults += [
                'form'  => $adminCrudConfigModel->getForm(),
                'paths' => [
                    'index' => $adminCrudConfigModel->getPath('index'),
                    'edit'  => $adminCrudConfigModel->getPath('edit')
                ]
            ];
        }
        else if($action === 'show') {
            $defaults += [
                'form'  => $adminCrudConfigModel->getForm(),
                'paths' => [
                    'index' => $adminCrudConfigModel->getPath('index'),
                ]
            ];
        }
        else if($action === 'edit') {
            $defaults += [
                'form'  => $adminCrudConfigModel->getForm(),
                'paths' => [
                    'index' => $adminCrudConfigModel->getPath('index'),
                    'edit'  => $adminCrudConfigModel->getPath('edit')
                ]
            ];
        }
        else if($action === 'delete') {
            $defaults += [
                'paths' => [
                    'index'  => $adminCrudConfigModel->getPath('index'),
                    'delete' => $adminCrudConfigModel->getPath('delete')
                ]
            ];
        }

        return $defaults;
    }
}
