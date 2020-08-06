<?php
namespace Sang\Repositories\Event;

use Sang\Repositories\Foundation\RepositoryEvent;

class RepositoryUpdatedEvent extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'updated';
}
