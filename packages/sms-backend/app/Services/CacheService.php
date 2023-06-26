<?php


namespace App\Services;


use App\Enums\CacheKey;
use App\Enums\CacheTags;
use App\Models\Channel;
use App\Models\Company;
use App\Models\SupervisorType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Get Company model for a given channel id
     * @param int $id
     * @return Company
     */
    public function companyOfChannel(int $id): Company
    {
        return $this->companies($this->channels($id)->company_id);
    }

    public function companies(int $id = null): Collection|Model
    {
        return $this->cacheGlobalModel(CacheKey::ALL_COMPANIES_COLLECTION, [CacheTags::COMPANY], Company::class, $id);
    }

    /**
     * Cache global model. Will cache the whole table of the model. Only use for frequently accessed, low memory,
     * rarely changed model such as company and channel.
     *
     * @param string $key
     * @param array $tags
     * @param string $class
     * @param int|null $id when provided, will return model of the given id instead of the whole model collection
     * @return Collection|Model
     */
    protected function cacheGlobalModel(string $key, array $tags, string $class, int $id = null): Collection|Model
    {
        $models = $this->cache($key, $tags, fn() => $class::all()->keyBy('id'));

        if (is_null($id)) return $models;

        // fallback protection, if we are not able to find the channel, it is possible that the
        // cache are outdated, so lets try refresh once before throwing error
        if (empty($models[$id])) {
            $this->forget($tags, $key);
            $models = $this->cache($key, $tags, fn() => $class::all()->keyBy('id'));
        }

        if (empty($models[$id])) abort(404);

        return $models[$id];
    }

    /**
     * @param string $key
     * @param array $tags
     * @param callable $function
     * @return mixed
     */
    public function cache(string $key, array $tags, callable $function): mixed
    {
        if (config('cache.default') !== 'redis') {
            return Cache::rememberForever($key, $function);
        }

        return Cache::tags($tags)->rememberForever($key, $function);
    }

    /**
     * @param array $tags arary of tags (string)
     */
    public function forget(array $tags, string $key = null)
    {
        if (config('cache.default') !== 'redis') {

            if ($key) {
                Cache::forget($key);
                return;
            }

            // Remove all cache keys associated to the tag
            collect($this->tagToKeyMapping())
                ->filter(function ($keys, $tag) use ($tags) {
                    return in_array($tag, $tags);
                })
                ->collapse()
                ->each(function (string $key) {
                    Cache::forget($key);
                });
        } else {

            if ($key) {
                Cache::tags($tags)->forget($key);
                return;
            }

            Cache::tags($tags)->flush();
        }
    }

    /**
     * @return array[]
     */
    public function tagToKeyMapping()
    {
        return [
            CacheTags::COMPANY => [
                CacheKey::ALL_COMPANIES_COLLECTION
            ],
            CacheTags::CHANNEL => [
                CacheKey::ALL_CHANNELS_COLLECTION
            ],
            CacheTags::SUPERVISOR_TYPE => [
                CacheKey::ALL_SUPERVISOR_TYPES_COLLECTION
            ],
        ];
    }

    public function channels(int $id = null): Collection|Model
    {
        return $this->cacheGlobalModel(CacheKey::ALL_CHANNELS_COLLECTION, [CacheTags::CHANNEL], Channel::class, $id);
    }

    public function supervisorTypes(int $id = null): Collection|Model
    {
        return $this->cacheGlobalModel(CacheKey::ALL_SUPERVISOR_TYPES_COLLECTION, [CacheTags::SUPERVISOR_TYPE], SupervisorType::class, $id);
    }

    public function channelsOfCompany(int $id): Collection
    {
        return $this->channels()->filter(function (Channel $channel) use ($id){
            return $channel->company_id === $id;
        });
    }

    public function channelsOfCompanies(array $company_ids): Collection
    {
        return $this->channels()->filter(function (Channel $channel) use ($company_ids){
            return in_array($channel->company_id, $company_ids);
        });
    }
}
