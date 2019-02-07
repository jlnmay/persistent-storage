<?php 

namespace JlnMay\PersistanceStorage; 

class Memcached 
{
    /* Memcached */
    private static 	$instance;
    private static $host = '127.0.0.1';
    private static $port = '11211';
    private static $tiime = 600;
    private static $memcached;

    /**
     * Returns the instance of this class
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
            self::getMemcachedInstance(); // create instance of memcache
        }
        
        return static::$instance;
    }
    
    /**
	 * Protected constructor to prevent creating a new instance of the
     * class via the `new` operator from outside of this class.
     */
    protected function __construct() 
    {
    }

    /**
	 * Private clone method to prevent cloning of the instance of the
     * class instance.
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the 
     * class instance.
     */
    private function __wakeup()
    {
    }

    /**
     * Connect to memcached and set instance
     *
     */
    private static function getMemcachedInstance()
    {
        if (null === static::$memcached) {
            if (class_exists('Memcached')) {
                static::$memcached = new Memcached();
                static::$memcached->addServer(self::$host,self::$port);
            } else {
                exit(json_encode(
                    array(
                        'error'=>1,
                        'errortext' => 'Missing Class Memcached, verify that package is installed on your server')
                        )
                    );
            }
        }
        
        return static::$memcached;
    }

    /**
     * Write data to storage
     */
    public function save($data = array())
    {
        if (isset($data['key']) && $data['key'] != "") {
            if (is_array($data['store'])) {
                $mc = static::$memcached;
                $mc->set($data['key'],serialize($data['store']), self::$_memcached_time); // Update to version memcached 3 removes second parameter
                return true;
            }
        }

       return false;
    }

    /**
     * If has data on storage return if not return false
     */
    public static function has($data = array()) {
        if (isset($data['key'])) {
            $mc = static::$memcached;
            $storedData = $mc->get($data['key']);
            if ($storedData) {
                return unserialize($storedData);
            }
        }

        return false;
    }
    
    /**
     * Clear one item out of storage
     */
    public static function clear($data = array())
    {
        if (isset($data['key'])) {
            $key = $data['key'];
            $mc = static::$memcached;
            $mc->delete($key);
            return true;
        }

        return false;
    }

    /**
     * Clear all items out of storage
     */
    public static function reset() 
    {
        $mc = static::$memcached;
        $mc->flush(0);
    }
}