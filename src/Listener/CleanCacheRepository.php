<?php

namespace Sang\Repository\Listener;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Sang\Repository\Contract\RepositoryInterface;
use Sang\Repository\Foundation\RepositoryEvent;
use Sang\Repository\Foundation\CacheKeys;

class CleanCacheRepository
{
    /**
     * @var CacheRepository $cache
     */
    protected $cache = null;

    /**
     * @var RepositoryInterface $repository
     */
    protected $repository = null;

    /**
     * @var Model $model
     */
    protected $model = null;

    /**
     * @var string $action
     */
    protected $action = null;

    /**
     * CleanCacheRepository constructor.
     */
    public function __construct()
    {
        $this->cache = app(config('repository.cache.repository', 'cache'));
    }

    /**
     * Handle
     *
     * @param RepositoryEvent $event
     */
    public function handle(RepositoryEvent $event)
    {
        try {
            $cleanEnabled = config("repository.cache.clean.enabled", true);

            if ($cleanEnabled) {
                $this->repository = $event->getRepository();
                $this->model = $event->getModel();
                $this->action = $event->getAction();

                if (config("repository.cache.clean.on.{$this->action}", true)) {
                    $cacheKeys = CacheKeys::getKeys(get_class($this->repository));

                    if (is_array($cacheKeys)) {
                        foreach ($cacheKeys as $key) {
                            $this->cache->forget($key);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}