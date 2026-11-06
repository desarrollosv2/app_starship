<?php defined('BASEPATH') or exit('No direct script access allowed');
use Predis\Client;

class RedisClient {
    /** @var Client */
    public $redis;
    public function __construct() {
        $this->redis = new Client([
            'scheme'   => 'tcp',
            'host'     => getenv('REDIS_HOST') ?: '127.0.0.1',
            'port'     => getenv('REDIS_PORT') ?: 6379,
            'password' => getenv('REDIS_PASSWORD') ?: null,
        ]);
    }
}
