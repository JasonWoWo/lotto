<?php


namespace Happy\Lotto\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class MigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lotto:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the Lotto specifications.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Execute the console command for Laravel 5.5+.
     *
     * @return void
     */
    public function handle()
    {
        $this->laravel->view->addNamespace('Lotto', substr(__DIR__, 0, -8).'views');

        $lottoActivity = Config::get('lotto.lotto_activity_table');
        $lottoActivityConfig = Config::get('lotto.lotto_config_table');
        $lottoActivityRecord = Config::get('lotto.lotto_record_table');
        $lottoPrize = Config::get('lotto.lotto_prize_table');
        $lottoRewardRecord = Config::get('lotto.lotto_reward_table');
        $lottoSponsor = Config::get('lotto.lotto_sponsor_table');

        $migrations = compact('lottoActivity', 'lottoActivityConfig', 'lottoActivityRecord', 'lottoPrize',
            'lottoRewardRecord', 'lottoSponsor');

        $this->line('');
        $this->info("Table: $lottoActivity, $lottoActivityConfig, $lottoActivityRecord, $lottoPrize,
         $lottoRewardRecord, $lottoSponsor ");

        $message = "A migration that creates '$lottoActivity', '$lottoActivityConfig', '$lottoActivityRecord',
         '$lottoPrize', '$lottoRewardRecord', '$lottoSponsor'".
            " tables will be created in database/migrations directory";

        $this->comment($message);
        $this->line('');

        if ($this->confirm("Proceed with the migration creation? [Yes|no]", "Yes")) {
            $this->line('');

            $this->info("Creating migration...");

            if ($this->createMigration($migrations)) {

                $this->info("Migration successfully created!");
            } else {
                $this->error(
                    "Couldn't create migration.\n Check the write permissions".
                    " within the database/migrations directory."
                );
            }
        }

        $this->line('');
    }

    protected function createMigration($migrations = [])
    {
        $migrationFile = base_path("/database/migrations")."/".date('Y_m_d_His')."_lotto_setup_tables.php";

        $output = $this->laravel->view->make('Lotto::generators.migration')->with($migrations)->render();

        if (!file_exists($migrationFile) && $fs = fopen($migrationFile, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }

        return false;
    }
}
