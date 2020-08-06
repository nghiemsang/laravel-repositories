<?php

namespace Sang\Repositories\Contract;

interface CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository);
}
