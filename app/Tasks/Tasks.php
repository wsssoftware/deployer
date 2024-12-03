<?php

namespace App\Tasks;

use Closure;
use Exception;
use Illuminate\Console\View\Components\Factory;

use function Laravel\Prompts\spin;

class Tasks
{
    /**
     * @var \App\Tasks\Task[]
     */
    protected array $tasks = [];

    protected Factory $components;

    public function call(string $name): void
    {
        if (! $this->has($name)) {
            throw new Exception("Task '{$name}' does not exist.");
        }
        $task = $this->tasks[$name];
        if ($task->isClosure()) {
            $start = microtime(true);
            $label = $task->label($name);
            $result = spin($task->closure, $label);
            $took = number_format(microtime(true) - $start, 1);
            $second = "<fg=gray>took {$took}s</>";
            $second .= ' <fg=green;options=bold>DONE</>';
            $this->components->twoColumnDetail($label, $second);
            if ($result instanceof Closure) {
                $result($this->components);
            }
        } else {
            foreach ($task->closure as $task) {
                $this->call($task);
            }
        }
    }

    public function has(string $name): bool
    {
        return isset($this->tasks[$name]);
    }

    public function initialize(Factory $components): void
    {
        $this->components = $components;
    }

    public function main(Closure|array $closure): Task
    {
        return $this->registry('main', $closure);
    }

    protected function registry(string $name, Closure|array $closure): Task
    {
        $this->tasks[$name] = new Task($closure);

        return $this->tasks[$name];
    }

    public function task(string $name, Closure|array $closure): Task
    {
        $name = trim(mb_strtolower($name));
        if ($name === 'main') {
            throw new Exception('Task "main" is a reserved task name');
        }

        return $this->registry($name, $closure);
    }
}
