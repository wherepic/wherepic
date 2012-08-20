<?php 

/*

|---------------------------------------------------------------
| 服务器缓存扩展 MEMCACHE
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------

*/
class cache_memcache {

	private $memcache = null;

	public function __construct() {
		$this->memcache = new Memcache;
		$this->memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT, MEMCACHE_TIMEOUT);
	}

	public function memcache() {
		$this->__construct();
	}

	public function get($name) {
		$value = $this->memcache->get($name);
		return $value;
	}

	public function set($name, $value, $ttl = 0, $ext1='', $ext2='') {
		return $this->memcache->set($name, $value, false, $ttl);
	}

	public function delete($name) {
		return $this->memcache->delete($name);
	}

	public function flush() {
		return $this->memcache->flush();
	}
}
?>