<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ExScrape\Client;

class defaultdata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $conn;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $conn)
    {
        parent::__construct();
        $this->conn = $conn;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $this->conn->fullUpdate();
    }
}
