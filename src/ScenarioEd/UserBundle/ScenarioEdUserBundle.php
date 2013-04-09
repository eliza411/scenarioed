<?php

namespace ScenarioEd\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ScenarioEdUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
