<?php

namespace Sang\Repositories\Contract;

interface CriteriaInterface
{
    /**
     * Apply criteria
     *
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository);
}
