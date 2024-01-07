<?php

enum TaskStatus: string
{
    case Done = 'Выполнено';
    case Undone = 'Невыполнено';
}

class tasksHandler
{
    public array $tasksList;
    private static int $maxTasks = 1000;
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
                    foreach ($this->tasksList as $key => $item) {
                        if ($item['Task ID'] === $taskId) {
                            break;
                        }
                        if ($key === array_key_last($this->tasksList)) {
                            break 2;
                        }
                    }
                    $taskId++;
                }
            }
            $this->tasksList[] = ['Task ID' => $taskId, 'Task priority' => $taskPriority, 'Task name' => $taskName, 'Task status' => TaskStatus::Undone];
            return "Задача добавлена.\n";
        }
        return "Задача не добавлена или из-за неправильного значения приоритета, или из-за достижения лимита задач.\n";
    }
    public function deleteTask(int $taskId): string
    {
        if ($this->tasksList) {
            if ($taskId >= 0) {
                foreach ($this->tasksList as $key => $item) {
                    if ($item['Task ID'] === $taskId) {
                        unset($this->tasksList[$key]);
                        $this->tasksList = array_values($this->tasksList);
                        return "Задача удалена.\n";
                    }
                }
                return "Задача c введенным ID не найдена.\n";
            }
            return "Задача не удалена из-за неправильно введенного ID.\n";
        }
        return "Список задач пуст. Удалять нечего.\n";
    }
    public function showTasks(): string
    {
        if (!$this->tasksList) {
            return "*****\nСписок задач пуст.\n*****\n";
        }
        echo "*****\nНачало списка задач.\n";
        foreach ($this->tasksList as $item) {
            echo "ID задачи: " .
                $item['Task ID'] .
                "; Приоритет задачи: " .
                $item['Task priority'] .
                "; Имя задачи: " .
                $item['Task name'] .
                "; Статус задачи: " .
                $item['Task status']->value .
                "." .
                "\n";
        }
        return "Конец списка задач.\n*****\n";
    }
    public function writeToFile(string $filename): string
    {
        $wData = "";
        foreach ($this->tasksList as $key => $item) {
            if ($key !== array_key_last($this->tasksList)) {
                $wData = $wData .
                    "ID задачи: " .
                    $item['Task ID'] .
                    "; Приоритет задачи: " .
                    $item['Task priority'] .
                    "; Имя задачи: " .
                    $item['Task name'] .
                    "; Статус задачи: " .
                    $item['Task status']->value .
                    ".\n";
            } else {
                $wData = $wData .
                    "ID задачи: " .
                    $item['Task ID'] .
                    "; Приоритет задачи: " .
                    $item['Task priority'] .
                    "; Имя задачи: " .
                    $item['Task name'] .
                    "; Статус задачи: " .
                    $item['Task status']->value .
                    ".";
            }
        }
        $wResult = file_put_contents($filename, $wData);
        if ($wResult > 0) {
            return "Данные успешно записаны.\n";
        } else {
            return "Данные записаны неуспешно.\n";
        }
    }
    public function readFromFile(string $filename): array
    {
        $rData = file_get_contents($filename);
        $secondaryRArray = [];
        $preResultRArray = [];
        $resultRArray = [];
        if ($rData !== "") {
            $mainRArray = explode("\n", $rData);
            foreach ($mainRArray as $key => $item) {
                $secondaryRArray[$key] = explode("; ", $item);
                $secondaryRArray[$key][3] = rtrim($secondaryRArray[$key][3], ".");
                foreach ($secondaryRArray[$key] as $secKey => $secItem) {
                    $tempArr = explode(": ", $secItem);
                    $preResultRArray[$key][$secKey] = $tempArr[1];
                }
            }
            foreach ($preResultRArray as $key => $item) {
                $resultRArray[$key] = ['Task ID' => (int) $item[0], 'Task priority' => (int) $item[1], 'Task name' => $item[2], 'Task status' => TaskStatus::from($item[3])];
            }
            $this->tasksList = $resultRArray;
        }
        return $resultRArray;
    }
    public function changeTaskStatus(int $taskId, $taskStatus): string
    {
        if ($this->tasksList) {
            if ($taskId >= 0) {
                $taskStatus = TaskStatus::tryFrom($taskStatus);
                if ($taskStatus === null) {
                    return "Вы ввели неверное значение статуса задачи. Статус задачи остался прежним.\n";
                }
                foreach ($this->tasksList as $key => $item) {
                    if ($item['Task ID'] === $taskId) {
                        $this->tasksList[$key]['Task status'] = $taskStatus;
                        return "Статус задачи изменен.\n";
                    }
                }
                return "Зачада с введенным ID не найдена.\n";
            }
            return "Вы ввели неправильное значение ID задачи. Статус задачи остался прежним.\n";
        }
        return "Список задач пуст. Изменять нечего.\n";
    }
}

$tasksList = [];
$tasksHandler = new tasksHandler($tasksList);
$tasksHandler->readFromFile("Tasks.txt");
echo $tasksHandler->showTasks();
while (true) {
    echo "Введите \"1\", что бы добавать задачу\n";
    echo "Введите \"2\", что бы удалить задачу\n";
    echo "Введите \"3\", что бы изменить статус задачи\n";
    $select = (int) readline("Ввод чего-либо другого прекратит выполнение программы: ");
    switch ($select) {
        case 1:
            $taskPriority = (int) readline("Введите приоритет задачи (от 1 до 15): ");
            $taskName = (string) readline("Введите имя задачи: ");
            echo $tasksHandler->addTask($taskName, $taskPriority);
            $tasksHandler->writeToFile("Tasks.txt");
            echo $tasksHandler->showTasks();
            break;
        case 2:
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");
            echo $tasksHandler->deleteTask($taskId);
            $tasksHandler->writeToFile("Tasks.txt");
            echo $tasksHandler->showTasks();
            break;
        case 3:
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");
            echo $tasksHandler->changeTaskStatus($taskId, readline("Введите статус задачи (\"Выполнено\" или \"Невыполнено\"): "));
            $tasksHandler->writeToFile("Tasks.txt");
            echo $tasksHandler->showTasks();
            break;
        default:
            break 2;
    }
}
var_dump($tasksList);
//$tasksList = ['Task ID' => TaskStatus::Undone];
//var_dump($tasksList);
//$a = TaskStatus::Done;
//$tasksList['Task ID'] = $a;
//var_dump($tasksList);
