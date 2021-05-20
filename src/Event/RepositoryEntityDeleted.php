<?php
namespace Sang\Repository\Event;

use Sang\Repository\Foundation\RepositoryEvent;

class RepositoryEntityDeleted extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'deleted';
}
