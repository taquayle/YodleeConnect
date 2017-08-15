<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use YodleeApi\Client;
use Illuminate\Contracts\Cache\Repository;
use \Cache;
class cacheTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yodlee:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
     protected $yodlee;
    public function __construct()
    {
        parent::__construct();
        //$this->yodlee = $yod;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $this->yodlee = new \YodleeApi\Client(env('YODLEEAPI_URL'));
      $response = $this->yodlee->cobrand()->login(env('YODLEEAPI_COBRAND_LOGIN'), env('YODLEEAPI_COBRAND_PASSWORD'));
      if($response)
        echo "CobrandApi:\t".$this->yodlee->session()->getCobrandSessionToken()."\n";
        echo "Caching Cobrand\n";
        $minutes = 10;
        Cache::put('cobrand', $this->yodlee->session()->getCobrandSessionToken(), $minutes);
        echo Cache::get('cobrand');
    }
}
