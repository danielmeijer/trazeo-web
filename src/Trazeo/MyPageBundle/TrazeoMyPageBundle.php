<?php

namespace Trazeo\MyPageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TrazeoMyPageBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}
