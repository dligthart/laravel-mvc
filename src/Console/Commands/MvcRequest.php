<?php

namespace Tychovbh\Mvc\Console\Commands;

use Illuminate\Console\Command;

class MvcRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mvc:request {name : The name of the class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Form Request class';

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
        $name = $this->argument('name');
        $file = sprintf('%s/Http/Requests/%s.php', app('path'), $name);

        file_replace(ucfirst(is_application()) .'Request.php', [
            'EntityRequest' => $name
        ], $file, __DIR__ . '/..');

        $this->line('Request created!');
    }
}
