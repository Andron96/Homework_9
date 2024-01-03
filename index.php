<?php
class tasksHandler
{
    public array $tasksList;
    private static $maxTasks = 1000;
    function __construct(array &$tasksList)
    {
        $this->tasksList = &$tasksList;
    }
    public function addTask(string $taskName, int $taskPriority): string
    {
        if (
            ($taskPriority >= 1)
            && ($taskPriority <= 15)
            && (count($this->tasksList) <= self::$maxTasks)
        ) {
            $taskId = 0;
            if ($this->tasksList) {
                while ($taskId <= self::$maxTasks) {
                    $taskId++;
                    foreach ($this->tasksList as $key => $item) {
                        if ($item['Task ID'] === $taskId) {
                            break;
                        }
                        if ($key === array_key_last($this->tasksList)) {
                            break 2;
                        }
                    }
                }
            }
            $this->tasksList[] = ['Task ID' => $taskId, 'Task priority' => $taskPriority, 'Task name' => $taskName];
            return "Задача добавлена.\n";
        }
        return "Задача не добавлена или из-за неправильного значения приоритета, или из-за достижения лимита задач.\n";
    }
    public function deleteTask(int $taskId)
    {
        if ($taskId > 0) {
            foreach ($this->tasksList as $key => $item) {
                if ($item['Task ID'] === $taskId) {
                    unset($this->tasksList[$key]);
                    $this->tasksList = array_values($this->tasksList);
                }
            }
        }
    }
    public function showTasks(): string
    {
        if (!$this->tasksList) {
            return "*****\nСписок задач пуст.\n*****\n";
        }
        echo "*****\nНачало списка задач.\n";
        foreach ($this->tasksList as $item) {
            echo "ID задачи: " . $item['Task ID'] . "; Приоритет задачи: " . $item['Task priority'] . "; Имя задачи: " . $item['Task name'] . "." . "\n";
        }
        return "Конец списка задач.\n*****\n";
    }
}

$tasksList = [];
$tasksHandler = new tasksHandler($tasksList);
while (true) {
    echo "Введите \"1\", что бы добавать задачу\n";
    echo "Введите \"2\", что бы удалить задачу\n";
    $select = (int) readline("Ввод чего-либо другого прекратит выполнение программы: ");
    switch ($select) {
        case 1:
            $taskPriority = (int) readline("Введите приоритет задачи (от 1 до 15): ");
            $taskName = (string) readline("Введите имя задачи: ");
            echo $tasksHandler->addTask($taskName, $taskPriority);
            echo $tasksHandler->showTasks();
            break;
        case 2:
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");
            $tasksHandler->deleteTask($taskId);
            echo $tasksHandler->showTasks();
            break;
        default:
            break 2;
    }
}
var_dump($tasksList);