<?php

namespace Sang\Repository\Event;

use Sang\Repository\Foundation\RepositoryEvent;

class RepositoryEntityCreated extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'created';
}
