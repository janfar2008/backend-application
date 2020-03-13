<?php

namespace Modules\TrelloIntegration\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\TrelloIntegration\Services\SyncTasks as Service;

class SyncTasks extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'trello:sync-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize tasks from Trello for all users, who activate the Trello integration.';

    /**
     * @var Service
     */
    protected Service $service;

    /**
     * Create a new command instance.
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->service->synchronizeAll();
    }
}
