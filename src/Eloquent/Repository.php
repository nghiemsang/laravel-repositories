<?php

namespace Sang\Repository\Eloquent;

use Closure;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Sang\Repository\Contract\CriteriaInterface;
use Sang\Repository\Contract\RepositoryCriteriaInterface;
use Sang\Repository\Contract\RepositoryInterface;
use Sang\Repository\Event\RepositoryEntityCreated;
use Sang\Repository\Event\RepositoryEntityCreating;
use Sang\Repository\Event\RepositoryEntityDeleted;
use Sang\Repository\Event\RepositoryEntityDeleting;
use Sang\Repository\Event\RepositoryEntityUpdated;
use Sang\Repository\Event\RepositoryEntityUpdating;
use Sang\Repository\Exception\RepositoryException;

abstract class Repository implements RepositoryInterface, RepositoryCriteriaInterface
{
    /**
     * @var Application $app
     */
    protected $app;

    /**
     * @var Model $model
     */
    protected $model;

    /**
     * Collection of Criteria
     *
     * @var Collection $criteria
     */
    protected $criteria;

    /**
     * @var bool $skipCriteria
     */
    protected $skipCriteria = false;

    /**
     * @var \Closure $scopeQuery
     */
    protected $scopeQuery = null;

    /**
     * Repository constructor.
     *
     * @param Application|null $app
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app = null)
    {
        $this->app      = $app ?? new Application();
        $this->criteria = new Collection();
        $this->makeModel();
        $this->boot();
    }

    /**
     *
     */
    public function boot()
    {
        //
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make Model
     *
     * @return Model|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Reset Model
     *
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Retrieve data array for populate field select
     *
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function lists(string $column, string $key = null)
    {
        $this->applyCriteria();

        return $this->model->lists($column, $key);
    }

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck(string $column, string $key = null)
    {
        $this->applyCriteria();

        return $this->model->pluck($column, $key);
    }

    /**
     * Sync relations
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @param bool $detaching
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sync($id, $relation, $attributes, bool $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function syncWithoutDetaching($id, $relation, $attributes)
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function all(array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();

        return $results;
    }

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function count(array $where = [], string $columns = '*')
    {
        $this->applyCriteria();
        $this->applyScope();

        if ($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->count($columns);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function first(array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->first($columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     * @return Collection|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function get(array $columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function firstOrNew(array $attributes = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->firstOrNew($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function firstOrCreate(array $attributes = [])
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->firstOrCreate($attributes);

        $this->resetModel();

        return $model;
    }

    /**
     * Retrieve data of repository with limit applied
     *
     * @param int $limit
     * @param array|string[] $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function limit(int $limit, array $columns = ['*'])
    {
        // Shortcut to all with `limit` applied on query via `take`
        $this->take($limit);

        return $this->all($columns);
    }

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
    public function paginate($limit = null, array $columns = ['*'], string $method = 'paginate')
    {
        $this->applyCriteria();
        $this->applyScope();

        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;

        $results = $this->model->{$method}($limit, $columns);
        $results->appends(app('request')->query());

        $this->resetModel();

        return $results;
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null $limit
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function simplePaginate($limit = null, array $columns = ['*'])
    {
        return $this->paginate($limit, $columns, 'simplePaginate');
    }

    /**
     * Find data by id
     *
     * @param $id
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function find($id, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model->findOrFail($id, $columns);

        $this->resetModel();

        return $model;
    }

    /**
     * Find data by field and value
     *
     * @param $field
     * @param null $value
     * @param array $columns
     * @return Collection|null
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findByField($field, $value, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->where($field, '=', $value)->get($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findWhere(array $where, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $this->applyConditions($where);

        $results = $this->model->get($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Find data by multiple values in one field
     *
     * @param string $field
     * @param array $values
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findWhereIn(string $field, array $values, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->whereIn($field, $values)->get($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Find data by excluding multiple values in one field
     *
     * @param string $field
     * @param array $values
     * @param array $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findWhereNotIn(string $field, array $values, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->whereNotIn($field, $values)->get($columns);

        $this->resetModel();

        return $results;
    }

    /**
     * Find data by between values in one field
     *
     * @param $field
     * @param array $values
     * @param array|string[] $columns
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function findWhereBetween($field, array $values, array $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereBetween($field, $values)->get($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(array $attributes)
    {
        event(new RepositoryEntityCreating($this, $attributes));
        $model = $this->model->newInstance($attributes);
        $model->save();

        $this->resetModel();

        event(new RepositoryEntityCreated($this, $model));

        return $model;
    }

    /**
     * Update a entity in repository by id
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update($id, array $attributes)
    {
        $this->applyScope();

        $model = $this->model->findOrFail($id);

        event(new RepositoryEntityUpdating($this, $model));

        $model->fill($attributes);
        $model->save();

        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $model;
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        $this->applyScope();

        event(new RepositoryEntityCreating($this, $attributes));

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     * @return int|mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function delete($id)
    {
        $this->applyScope();

        $model         = $this->find($id);
        $originalModel = clone $model;

        $this->resetModel();

        event(new RepositoryEntityDeleting($this, $model));

        $deleted = $model->delete();

        event(new RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function deleteWhere(array $where)
    {
        $this->applyScope();
        $this->applyConditions($where);

        event(new RepositoryEntityDeleting($this, $this->model->getModel()));

        $deleted = $this->model->delete();

        event(new RepositoryEntityDeleted($this, $this->model->getModel()));

        $this->resetModel();

        return $deleted;
    }

    /**
     * Check if entity has relation
     *
     * @param $relation
     * @return $this
     */
    public function has($relation): self
    {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Load relations
     *
     * @param $relations
     * @return $this
     */
    public function with($relations): self
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Add subselect queries to count the relations.
     *
     * @param mixed $relations
     * @return $this
     */
    public function withCount($relations): self
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param Closure $closure
     * @return $this
     */
    public function whereHas(string $relation, Closure $closure): self
    {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields
     * @return $this
     */
    public function hidden(array $fields): self
    {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Set order by
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take(int $limit): self
    {
        // Internally `take` is an alias to `limit`
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     * @return $this
     */
    public function visible(array $fields): self
    {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     * @return $this
     * @throws RepositoryException
     */
    public function pushCriteria($criteria): self
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new RepositoryException("Class " . get_class($criteria) . " must be an instance of Prettus\\Repository\\Contracts\\CriteriaInterface");
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Pop Criteria
     *
     * @param $criteria
     * @return $this
     */
    public function popCriteria($criteria): self
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria(): Collection
    {
        return $this->criteria;
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     * @return mixed
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        $results     = $this->model->get();
        $this->resetModel();

        return $results;
    }

    /**
     * Skip Criteria
     *
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true): self
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Reset all Criteria
     *
     * @return $this
     */
    public function resetCriteria(): self
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Query Scope
     *
     * @param Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(Closure $scope): self
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope(): self
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope(): self
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback    = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria(): self
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Trigger static method calls to the model
     *
     * @param $method
     * @param $arguments
     * @return false|mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * Trigger method calls to the model
     *
     * @param string $method
     * @param array $arguments
     * @return false|mixed
     */
    public function __call(string $method, array $arguments)
    {
        $this->applyCriteria();
        $this->applyScope();

        return call_user_func_array([$this->model, $method], $arguments);
    }
}
