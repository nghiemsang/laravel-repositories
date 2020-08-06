<?php
namespace Sang\Repositories\Event;

use Sang\Repositories\Foundation\RepositoryEvent;

class RepositoryDeletedEvent extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'deleted';
}
