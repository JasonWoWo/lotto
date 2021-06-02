<?php


namespace Happy\Lotto\Commands;

use Illuminate\Console\Command;

class DrawLottery extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lotto:draw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Draw activity lottery for lucky(lotto活动开奖并发放奖品)';

    public function __construct()
    {
        parent::__construct();

    }

    public function fire()
    {
        $this->handle();
    }

    public function handle()
    {

    }
}
