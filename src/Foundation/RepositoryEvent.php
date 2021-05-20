<?php

namespace Sang\Repository\Foundation;

use Illuminate\Database\Eloquent\Model;
use Sang\Repository\Contract\RepositoryInterface;

abstract class RepositoryEvent
{
    /**
     * @var Model $model
     */
    protected $model;

    /**
     * @var RepositoryInterface $repository
     */
    protected $repository;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * RepositoryEvent constructor.
     *
     * @param RepositoryInterface $repository
     * @param Model $model
     */
    public function __construct(RepositoryInterface $repository, Model $model)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
}
