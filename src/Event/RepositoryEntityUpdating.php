<?php

namespace Sang\Repository\Event;

use Sang\Repository\Foundation\RepositoryEvent;

class RepositoryEntityUpdating extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'updating';
}
