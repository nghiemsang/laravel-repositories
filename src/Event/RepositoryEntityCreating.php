<?php

namespace Sang\Repository\Event;

use Sang\Repository\Foundation\RepositoryEvent;

class RepositoryEntityCreating extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'creating';
}
