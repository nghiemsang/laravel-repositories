<?php

namespace Sang\Repositories\Criteria;

use Illuminate\Support\Facades\Schema;
use Sang\Repositories\Contract\CriteriaInterface;
use Sang\Repositories\Contract\RepositoryInterface;

class SortBuilderTableCriteria implements CriteriaInterface
{
    /**
     * Apply criteria
     *
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $sort = app('request')->input(config('repository.sort_builder_table.limit', 'sort'));
        if (!empty($sort)) {
            if (is_array($sort)) {
                foreach ($sort as $key => $value) {
                    if (in_array($key, Schema::getColumnListing($model->getTable()))) {
                        $model = $model->orderBy($key, $value);
                    }
                }
            }
        }

        return $model;
    }
}