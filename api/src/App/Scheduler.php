<?php

declare(strict_types=1);

namespace App;

class Scheduler {

    public $userOne;
    public $userTwo;
    public $userThree;
    public array $users = [];
    public $activityOne;
    public $activityTwo;
    public $activityThree;
    public array $activities = [];

    public function __construct()
    {
        $this->userOne = new User();
        $this->userTwo = new User();
        $this->userThree = new User();

        $this->userOne->setAvailability(DayOfWeek::Monday, new TimeRange(12.00, 13.00));
        $this->userOne->userName = "Andy";
        $this->userOne->userId = 1;

        $this->userTwo->setAvailability(DayOfWeek::Tuesday, new TimeRange(12.00, 13.00));
        $this->userTwo->userName = "Roy";
        $this->userTwo->userId = 2;

        $this->userThree->setAvailability(DayOfWeek::Wednesday, new TimeRange(12.00, 13.00));
        $this->userThree->userName = "Filip";
        $this->userThree->userId = 3;

        $this->users = [$this->userOne, $this->userTwo, $this->userThree];

        $this->activityOne = new Task();
        $this->activityTwo = new Task();
        $this->activityThree = new Task();

        $this->activityOne->activityId = 1;
        $this->activityOne->activityName = "Serving Food";
        $this->activityOne->startTime = new \DateTime('2024-02-28 12:30:00');
        $this->activityOne->endTime = new \DateTime('2024-02-28 13:00:00');

        $this->activityTwo->activityId = 2;
        $this->activityTwo->activityName = "Walking Dogs";
        $this->activityTwo->startTime = new \DateTime('2024-02-27 12:30:00');
        $this->activityTwo->endTime = new \DateTime('2024-02-27 13:00:00');

        $this->activityThree->activityId = 3;
        $this->activityThree->activityName = "Answering Calls";
        $this->activityThree->startTime = new \DateTime('2024-02-26 12:30:00');
        $this->activityThree->endTime = new \DateTime('2024-02-26 13:00:00');

        $this->activities = [$this->activityOne, $this->activityTwo, $this->activityThree];
    }

    public function getManagerSchedule()
{
    $schedule = [];
    foreach ($this->activities as $activity)
    {
        $activityDayOfWeek = $activity->startTime->format('l');

        foreach($this->users as $user)
        {
            if (isset($user->availability[$activityDayOfWeek]) && $user->availability[$activityDayOfWeek] !== null)
                {
                    $userAvailableStart = $user->availability[$activityDayOfWeek]->start;
                    $userAvailableEnd = $user->availability[$activityDayOfWeek]->end;

                    $activityStart = $activity->startTime->format('H') + $activity->startTime->format('i') / 60;
                    $activityEnd = $activity->endTime->format('H') + $activity->endTime->format('i') / 60;

               if(($activityStart < $userAvailableEnd) && ($activityEnd > $userAvailableStart))
                 {
                    $schedule[$activity->activityName][] =
                    ['user' => $user->userName,
                    'startTime' => $activity->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $activity->endTime->format('Y-m-d H:i:s')
                ];
                 }
            }
        }
    }
    return  $schedule;
}
}
