# Introducción

La función de este bundle es la de administrar un CRUD básico para cualquier Entity sin tener que generar controladores adicionales para ello.

# 1. Instalación

Instalar el bundle mediante composer:

```
composer require goleadsit/admincrudbundle "~2.0"
```

o

```js
// composer.json

"repositories": [
    {
      "type": "vcs",
      "url": "git@bitbucket.org:goleadsit/admincrudbundle.git"
    }
 ],
 "require": {
 	"goleadsit/admincrudbundle": "~2.0"
 }
```
# 2. Habilitar el Bundle

Hay que habilitar 3 Bundles [AdminBundle](https://bitbucket.org/goleadsit/adminbundle/src/master/), AdminCrudBundle y [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)

**Symfony 3**

```php
// app/AppKernel.php

<?php

public function registerBundles() {
	$bundles = array(
		// ...
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        new Goleadsit\AdminBundle\GoleadsitAdminBundle(),
		new Goleadsit\AdminBundle\GoleadsitAdminCrudBundle(),
	);
}
```

**Symfony 4**

```php
// config/bundles.php

<?php

return [
    // ...
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
    Goleadsit\AdminBundle\GoleadsitAdminBundle::class => ['all' => true],
    Goleadsit\AdminBundle\GoleadsitAdminCrudBundle::class => ['all' => true],
];
```

Después de habilitar los bundles hay que instalar los assets necesarios mediante el comando.
```php
bin\console assets:install
```

# 3. Incluir rutas

```yaml
# symfony 3 app/config/routing.yml
# symfony 4 config/routes.yaml

admincrud_routes:
    resource: '@GoleadsitAdminCrudBundle/Resources/config/router.yaml'
```

# 4. Guía de uso
Para empezar a utilizar este bundle es necesario al menos una Entity que añadiremos a los archivos de configuración para que comience la magia.

```yaml
# symfony 3 app/config/config.yml
# symfony 4 config/packages/goleadsit_admin_crud.yaml

goleadsit_admin_crud:
     App\Entity\Demo: #Entity Namespace
        form: App\Form\DemoType #Namespace del formulario
        route: demo #Prefijo de ruta para la entity
        actions: #Habilitar o Deshabilitar acciones del CRUD (Opcional)
            index: true
            new: true
            edit: true
            show: true
            delete: true
        paths: #Nombre que se asociará a una ruta, en caso de no incluir ninguno se generan por defecto (Opcional)
            index: null # admincrud_demo_index
            new: null # admincrud_demo_new
            edit: null # admincrud_demo_edit
            show: null # admincrud_demo_show
            delete: null # admincrud_demo_delete
```

**¡TERMINADO!**

Si ahora intentamos acceder a cualquiera de las rutas del CRUD para nuestra Entity desde el navegador podemos ver un panel de administración con los datos necesarios para *ver*, *editar*, *insertar*, *eliminar* o *ver en detalle* nuestra Entity.

# 5. Uso más avanzado

En esta sección se explicará como gestionar una acción desde un controlador propio o como cambiar una vista.

## 5.1. Utilizar un controlador propio

Para dotar de una mayor funcionalidad a cualquiera de las acciones del CRUD solo tienes que deshabilitar dicha acción en la configuración y gestionarla desde cualquier controlador propio, tal y como lo harías sin este bundle.

A esta acción probablemente le darás un nombre de ruta que deberás configurar para que todas las demás funcionen en armonía.

En este ejemplo crearemos la acción Index aparte.

```php
<?php

namespace App\Controller;

use App\Entity\Demo;
use App\Repository\DemoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/demo")
 */
class DemoController extends AbstractController
{
    /**
     * @Route("/", name="demo_index", methods={"GET"})
     */
    public function index(DemoRepository $demoRepository): Response
    {
        return $this->render('demo/index.html.twig', [
            'demos' => $demoRepository->findAll(),
        ]);
    }
}
```

```yaml
# symfony 3 app/config/config.yml
# symfony 4 config/packages/goleadsit_admin_crud.yaml

goleadsit_admin_crud:
     App\Entity\Demo:
        form: App\Form\DemoType
        route: demo
        actions:
            index: false
        paths:
            index: 'demo_index'
```

Con esto cada vez que se accediera a `/demo` el controlador que funcionaría sería `DemoController` y en caso de acceder por ejemplo a `/demo/new` funcionaría a través del bundle y el botón *Volver* enlazaría correctamente con la ruta `demo_index`.

## 5.2. Incluir templates propios

Se pueden incluir templates propios bien sobrescribiendo para cada acción, lo único que hay que hacer es:

1. Crear una carpeta bajo `app/Resources/views` (Symfony 3) o `templates` (Symfony 4) con el mismo nombre de la Entity en minúsculas.
2. En esta carpeta incluir un archivo `.html.twig` con el nombre de la acción a modificar `index`, `show`, `new`, `edit` o `delete`.
3.  (Opcional) Para mantener el formato del administrador solo hay que extender la plantilla base y sobrescribir los bloques necesarios ([AdminBundle](https://bitbucket.org/goleadsit/adminbundle/src/master/README.md))

```twig
{% extends '@GoleadsitAdminCrud/base.html.twig' %}
```

También es posible sobrescribir únicamente la vista del formulario, este template se incluye en `new`, `edit` y `show`.

Para cambiarlo basta con generar un archivo con el nombre `form.html.twig` bajo la carpeta creada anteriormente.

