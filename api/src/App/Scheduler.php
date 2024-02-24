<?php

declare(strict_types=1);

namespace App;

class Scheduler {

    public $userOne;
    public $userTwo;
    public $userThree;
    public array $users = [];
    public $taskOne;
    public $taskTwo;
    public $taskThree;
    public array $tasks = [];
    
    public function __construct()
    {
        $this->userOne = new User();
        $this->userTwo = new User();
        $this->userThree = new User();

        $this->userOne->setAvailability(new \DateTime('1970-01-01 12:00:00'), new \DateTime('1970-01-01 13:00:00'),
        null,null,null,null,null,null,null,null,null,null,null,null);
        $this->userOne->userName = "Andy";
        $this->userOne->userId = 1;

        $this->userTwo->setAvailability(null,null,new \DateTime('1970-01-01 12:00:00'), new \DateTime('1970-01-01 13:00:00'),
        null,null,null,null,null,null,null,null,null,null);     
        $this->userTwo->userName = "Roy";
        $this->userTwo->userId = 2;

        $this->userThree->setAvailability(null,null,null,null,new \DateTime('1970-01-01 12:00:00'), new \DateTime('1970-01-01 13:00:00'),
        null,null,null,null,null,null,null,null);
        $this->userThree->userName = "Filip";
        $this->userThree->userId = 3;

        $this->users = [$this->userOne, $this->userTwo, $this->userThree];

        $this->taskOne = new Task();
        $this->taskTwo = new Task();
        $this->taskThree = new Task();

        $this->taskOne->taskId = 1;
        $this->taskOne->taskName = "Serving Food";
        $this->taskOne->startTime = new \DateTime('2024-02-28 12:30:00');
        $this->taskOne->endTime = new \DateTime('2024-02-28 13:00:00');

        $this->taskTwo->taskId = 2;
        $this->taskTwo->taskName = "Walking Dogs";
        $this->taskTwo->startTime = new \DateTime('2024-02-27 12:30:00');
        $this->taskTwo->endTime = new \DateTime('2024-02-27 13:00:00');

        $this->taskThree->taskId = 3;
        $this->taskThree->taskName = "Answering Calls";
        $this->taskThree->startTime = new \DateTime('2024-02-26 12:30:00');
        $this->taskThree->endTime = new \DateTime('2024-02-26 13:00:00');

        $this->tasks = [$this->taskOne, $this->taskTwo, $this->taskThree];
    }

    public function getManagerSchedule()
{
    $schedule = [];
    foreach ($this->tasks as $task)
    {
        $taskDayOfWeek = $task->startTime->format('l');

        foreach($this->users as $user) 
        {
            if($user->availability[$taskDayOfWeek]["startTime"] !== null)
            {
                $userAvailableStart = new \DateTime($user->availability[$taskDayOfWeek]["startTime"]->format('H:i:s'));
                $userAvailableEnd= new \DateTime($user->availability[$taskDayOfWeek]["endTime"]->format('H:i:s'));
                $taskStart = new \DateTime($task->startTime->format('H:i:s'));
                $taskEnd = new \DateTime($task->endTime->format('H:i:s'));
               if(($taskStart < $userAvailableEnd) && ($taskEnd > $userAvailableStart))
                 {
                    $schedule[$task->taskName][] = $user->userName;
                 }
            }
        }
    } 
    return  $schedule;
}
}