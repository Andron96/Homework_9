<?php
class tasksHandler
{
    public array $tasksList;
    public string $taskName;
    public int $taskPriority;
    private int $taskId;
    function __construct(array &$tasksList)
    {
        $this->tasksList = &$tasksList;
    }
    public function addTask(string $taskName, int $taskPriority)
    {
        $this->tasksList[] = ['Task name' => $taskName, 'Task priority' => $taskPriority];
    }
}

$tasksList = [];
$tasksHandler = new tasksHandler($tasksList);
$tasksHandler->addTask("new task", 1);
var_dump($tasksList);