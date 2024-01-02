<?php
class tasksHandler
{
    public array $tasksList;
    function __construct(array &$tasksList)
    {
        $this->tasksList = &$tasksList;
    }
    public function addTask(string $taskName, int $taskPriority): string
    {
        if (($taskPriority >= 1) && ($taskPriority <= 15)) {
            if (!$this->tasksList) {
                $taskId = 0;
            } else {
                $taskId = array_key_last($this->tasksList) + 1;
            }
            $this->tasksList[] = ['Task ID' => $taskId, 'Task priority' => $taskPriority, 'Task name' => $taskName];
            echo "Задача добавлена.\n";
        } else {
            echo "Задача не добавлена из-за неправильного значения приоритета.\n";
        }
        if (!$this->tasksList) {
            return "*****\nСписок задач пуст.\n*****\n";
        } else {
            echo "*****\nНачало списка задач.\n";
            foreach ($this->tasksList as $item) {
                echo "ID задачи: " . $item['Task ID'] . "; Приоритет задачи: " . $item['Task priority'] . "; Имя задачи: " . $item['Task name'] . "." . "\n";
            }
            return "Конец списка задач.\n*****\n";
        }
    }
    public function deleteTask(int $taskId)
    {

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
            break;
        default:
            break 2;
    }
}