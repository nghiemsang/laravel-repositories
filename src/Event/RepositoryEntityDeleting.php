<?php

namespace Sang\Repository\Event;

use Sang\Repository\Foundation\RepositoryEvent;

class RepositoryEntityDeleting extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'deleting';
}
