<?php

namespace Sang\Repository\Contract;

use Illuminate\Support\Collection;

interface RepositoryCriteriaInterface
{
    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return mixed
     */
    public function pushCriteria($criteria);

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return mixed
     */
    public function popCriteria($criteria);

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria(): Collection;

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria);

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return mixed
     */
    public function skipCriteria($status = true);

    /**
     * Reset all Criteria(s)
     *
     * @return mixed
     */
    public function resetCriteria();
}
