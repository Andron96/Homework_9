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
            $this->tasksList[] = [
                'Task ID' => $taskId,
                'Task priority' => $taskPriority,
                'Task name' => $taskName,
                'Task status' => TaskStatus::Undone
            ];
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
    public function getSortByPriorityTasksList(): array
    {
        $sortedTasksList = [];
        if ($this->tasksList) {
            for ($priority = 15; $priority >= 1; $priority--) {
                foreach ($this->tasksList as $item) {
                    if ($item['Task priority'] === $priority) {
                        $sortedTasksList[] = $item;
                    }

                }
            }
            echo "*****\nНачало отсортированного списка задач.\n";
            foreach ($sortedTasksList as $item) {
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
            echo "Конец отсортированного списка задач.\n*****\n";
        } else {
            echo "Список задач пуст. Сортировать нечего.\n";
        }
        return $sortedTasksList;
    }
    public function writeToFile(string $filename): string
    {
        $wData = "";
        foreach ($this->tasksList as $key => $item) {
            $wData = $wData .
                "ID задачи: " .
                $item['Task ID'] .
                "; Приоритет задачи: " .
                $item['Task priority'] .
                "; Имя задачи: " .
                $item['Task name'] .
                "; Статус задачи: " .
                $item['Task status']->value;
            if ($key !== array_key_last($this->tasksList)) {
                $wData .= ".\n";
            } else {
                $wData .= ".";
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
        $resultRArray = [];
        if (file_exists($filename)) {
            $rData = file_get_contents($filename);
            if ($rData !== "") {
                $tempRArray = explode("\n", $rData);
                foreach ($tempRArray as $key => $item) {
                    $item = rtrim($item, ".");
                    $item = str_replace("ID задачи: ", "", $item);
                    $item = str_replace(" Приоритет задачи: ", "", $item);
                    $item = str_replace(" Имя задачи: ", "", $item);
                    $item = str_replace(" Статус задачи: ", "", $item);
                    $tempRArray[$key] = explode(";", $item);
                }
                foreach ($tempRArray as $key => $item) {
                    $resultRArray[$key] = [
                        'Task ID' => (int) $item[0],
                        'Task priority' => (int) $item[1],
                        'Task name' => $item[2],
                        'Task status' => TaskStatus::from($item[3])
                    ];
                }
                $this->tasksList = $resultRArray;
            }
        } else {
            echo "Файл со списком задач не найден.\n";
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
