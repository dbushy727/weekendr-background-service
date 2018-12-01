<?php

namespace Weekendr\Console\Commands;

use Illuminate\Console\Command;

class GetFlightDealsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skyscanner:get-flight-deals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hit the Skyscanner API and get flight deals';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
