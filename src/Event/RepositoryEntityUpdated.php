<?php
namespace Sang\Repository\Event;

use Sang\Repository\Foundation\RepositoryEvent;

class RepositoryEntityUpdated extends RepositoryEvent
{
    /**
     * @var string
     */
    protected $action = 'updated';
}
