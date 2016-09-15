<?php 

namespace Auth0\Login;

use Illuminate\Cache\Repository;
use Auth0\SDK\Helpers\Cache\CacheHandler;

class LaravelCacheWrapper implements CacheHandler 
{
  protected $cache;

  public function __construct(Repository $laravelCache) 
  {
    $this->cache = $laravelCache;
  }

  public function get($key) 
  {
    return $this->cache->get($key);
  }

  public function delete($key) 
  {
    $this->cache->forget($key);
  }
  
  public function set($key, $value) 
  {
    $this->cache->forever($key, $value);
  }
}