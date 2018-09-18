<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        Cache::add('test.redis.store', 'true', 10);
        return 1;
    }

    public function getRedisCache()
    {
        return Cache::get('test.redis.store');
    }
}