<?php

namespace Sang\Repository\Contract;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

interface CacheableInterface
{
    /**
     * Set Cache Repository
     *
     * @param CacheRepository $repository
     *
     * @return mixed
     */
    public function setCacheRepository(CacheRepository $repository);

    /**
     * Return instance of Cache Repository
     *
     * @return CacheRepository
     */
    public function getCacheRepository(): CacheRepository;

    /**
     * Get Cache key for the method
     *
     * @param $method
     * @param $args
     *
     * @return string
     */
    public function getCacheKey($method, $args = null): string;

    /**
     * Get cache minutes
     *
     * @return int
     */
    public function getCacheMinutes(): int;


    /**
     * Skip Cache
     *
     * @param bool $status
     *
     * @return mixed
     */
    public function skipCache(bool $status = true);
}