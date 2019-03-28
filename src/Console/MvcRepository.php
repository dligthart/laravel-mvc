<?php

namespace Tychovbh\Mvc\Console;

use Illuminate\Console\Command;

class MvcRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mvc:repository {name : The name of the class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Repository class';

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
        $directory = app('path') . '/Repositories';

        if (!is_dir($directory)) {
            mkdir($directory);
        }
        $output = "<?php\n\n";
        $output .= "namespace App\Repositories;\n\n";
        $output .= "use Tychovbh\Mvc\Repositories\Repository;\n";
        $output .= "use Tychovbh\Mvc\Repositories\AbstractRepository;\n\n";
        $output .= "class " . $name . " extends AbstractRepository implements Repository\n";
        $output .= "{\n";
        $output .= "    //\n";
        $output .= "}\n";
        $file = $directory . '/' . $name . '.php';
        shell_exec('touch ' . $file);
        shell_exec(sprintf('echo "%s" > %s', $output, $file));
    }
}