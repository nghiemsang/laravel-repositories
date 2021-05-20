<?php

namespace Sang\Repository\Contract;

use Closure;
use Sang\Repository\Exception\RepositoryException;

interface RepositoryInterface
{
    /**
     * Retrieve data array for populate field select
     *
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists(string $column, string $key = null);

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck(string $column, string $key = null);

    /**
     * Sync relations
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @param bool $detaching
     * @return mixed
     */
    public function sync($id, $relation, $attributes, bool $detaching = true);

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @return mixed
     */
    public function syncWithoutDetaching($id, $relation, $attributes);

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all(array $columns = ['*']);

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function count(array $where = [], string $columns = '*');

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function first(array $columns = ['*']);

    /**
     * Alias of All method
     *
     * @param array $columns
     * @return Collection|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function get(array $columns = ['*']);

    /**
     * Retrieve data of repository with limit applied
     *
     * @param int $limit
     * @param array|string[] $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function limit(int $limit, array $columns = ['*']);

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null $limit
     * @param array $columns
     * @param string $method
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function paginate($limit = null, array $columns = ['*'], string $method = 'paginate');

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, array $columns = ['*']);

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, array $columns = ['*']);

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value, array $columns = ['*']);

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, array $columns = ['*']);

    /**
     * Find data by multiple values in one field
     *
     * @param string $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereIn(string $field, array $values, array $columns = ['*']);

    /**
     * Find data by excluding multiple values in one field
     *
     * @param string $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereNotIn(string $field, array $values, array $columns = ['*']);

    /**
     * Find data by between values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereBetween($field, array $values, array $columns = ['*']);

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update a entity in repository by id
     *
     * @param       $id
     * @param array $attributes
     *
     * @return mixed
     */
    public function update($id, array $attributes);

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateOrCreate(array $attributes, array $values = []);

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     * @return int|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function delete($id);

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteWhere(array $where);

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return mixed
     */
    public function orderBy(string $column, string $direction = 'asc');

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take(int $limit);

    /**
     * Check if entity has relation
     *
     * @param $relation
     * @return mixed
     */
    public function has($relation);

    /**
     * Load relations
     *
     * @param $relations
     *
     * @return $this
     */
    public function with($relations);

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param closure $closure
     *
     * @return $this
     */
    public function whereHas(string $relation, Closure $closure);

    /**
     * Add subselect queries to count the relations.
     *
     * @param mixed $relations
     * @return $this
     */
    public function withCount($relations);

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields);

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields);

    /**
     * Query Scope
     *
     * @param Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(Closure $scope);

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope();

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function firstOrNew(array $attributes = []);

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function firstOrCreate(array $attributes = []);
}
