<?php
namespace Sang\Repositories\Event;

use Sang\Repositories\Foundation\RepositoryEvent;

class RepositoryCreatedEvent extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'created';
}
