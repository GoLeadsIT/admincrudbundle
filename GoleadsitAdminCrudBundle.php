<?php

namespace Goleadsit\AdminCrudBundle;

use Goleadsit\AdminCrudBundle\DependencyInjection\GoleadsitAdminCrudExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GoleadsitAdminCrudBundle extends Bundle {

    public function getContainerExtension() {
        if($this->extension === NULL) {
            $this->extension = new GoleadsitAdminCrudExtension();
        }

        return $this->extension;
    }
}
