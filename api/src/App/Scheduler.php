<?php

declare(strict_types=1);

namespace App;

class Scheduler
{

    public $userOne;
    public $userTwo;
    public $userThree;
    public array $users = [];
    public $activityOne;
    public $activityTwo;
    public $activityThree;

    public $activityFour;
    public array $activities = [];
    public array $activityRatings = [];
    public array $scheduledTimeSlots = [];
    public bool $overlap;

    public function __construct()
    {
        $this->userOne = new User();
        $this->userTwo = new User();
        $this->userThree = new User();

        $this->userOne->setAvailability(DayOfWeek::Monday, new TimeRange(12.00, 13.00));
        $this->userOne->setAvailability(DayOfWeek::Tuesday, new TimeRange(12.00, 13.00));
        $this->userOne->setAvailability(DayOfWeek::Wednesday, new TimeRange(12.00, 13.00));
        $this->userOne->userName = "Andy";
        $this->userOne->userId = 1;

        $this->userTwo->setAvailability(DayOfWeek::Monday, new TimeRange(12.00, 13.00));
        $this->userTwo->setAvailability(DayOfWeek::Tuesday, new TimeRange(12.00, 13.00));
        $this->userTwo->setAvailability(DayOfWeek::Wednesday, new TimeRange(12.00, 13.00));
        $this->userTwo->userName = "Roy";
        $this->userTwo->userId = 2;

        $this->userThree->setAvailability(DayOfWeek::Monday, new TimeRange(12.00, 13.00));
        $this->userThree->setAvailability(DayOfWeek::Tuesday, new TimeRange(12.00, 13.00));
        $this->userThree->setAvailability(DayOfWeek::Wednesday, new TimeRange(12.00, 13.00));
        $this->userThree->userName = "Filip";
        $this->userThree->userId = 3;

        $this->users = [$this->userOne, $this->userTwo, $this->userThree];

        $this->activityOne = new Task();
        $this->activityTwo = new Task();
        $this->activityThree = new Task();
        $this->activityFour = new Task();

        $this->activityOne->activityId = 1;
        $this->activityOne->activityName = "Serving Food";
        $this->activityOne->volunteerSlots = 2;
        $this->activityOne->startTime = new \DateTime('2024-02-28 12:00:00');
        $this->activityOne->endTime = new \DateTime('2024-02-28 13:00:00');

        $this->activityTwo->activityId = 2;
        $this->activityTwo->activityName = "Walking Dogs";
        $this->activityTwo->volunteerSlots = 1;
        $this->activityTwo->startTime = new \DateTime('2024-02-27 12:00:00');
        $this->activityTwo->endTime = new \DateTime('2024-02-27 13:00:00');

        $this->activityThree->activityId = 3;
        $this->activityThree->activityName = "Answering Calls";
        $this->activityThree->volunteerSlots = 3;
        $this->activityThree->startTime = new \DateTime('2024-02-26 12:00:00');
        $this->activityThree->endTime = new \DateTime('2024-02-26 13:00:00');

        $this->activityFour->activityId = 4;
        $this->activityFour->activityName = "Cleaning";
        $this->activityFour->volunteerSlots = 3;
        $this->activityFour->startTime = new \DateTime('2024-02-26 12:00:00');
        $this->activityFour->endTime = new \DateTime('2024-02-26 13:00:00');

        $this->activities = [$this->activityOne, $this->activityTwo, $this->activityThree, $this->activityFour];
        $this->activityRatings = array(
            new Rating(1, 1, 5),
            new Rating(1, 2, 2),
            new Rating(1, 3, 4),
            new Rating(2, 1, 2),
            new Rating(2, 2, 5),
            new Rating(2, 3, 1),
            new Rating(3, 1, 1),
            new Rating(3, 2, 1),
            new Rating(3, 3, 5)
        );
    }

    public function getManagerSchedule()
    {
        $schedule = [];
        foreach ($this->activities as $activity) {
            $activityDayOfWeek = $activity->startTime->format('l');
            $volunteerSlotsFilled = 0;

            foreach ($this->users as $user) {

                if (isset($user->availability[$activityDayOfWeek]) && $user->availability[$activityDayOfWeek] !== null) {
                    $userAvailableStart = $user->availability[$activityDayOfWeek]->start;
                    $userAvailableEnd = $user->availability[$activityDayOfWeek]->end;

                    $activityStart = $activity->startTime->format('H') + $activity->startTime->format('i') / 60;
                    $activityEnd = $activity->endTime->format('H') + $activity->endTime->format('i') / 60;

                    if (($activityStart < $userAvailableEnd) && ($activityEnd > $userAvailableStart) && ($volunteerSlotsFilled < $activity->volunteerSlots)) {

                        $schedule[$activity->activityName][$activity->startTime->format('Y-m-d H:i')][$activity->endTime->format('Y-m-d H:i')][] =
                            [
                                'user' => $user->userName
                            ];
                        $volunteerSlotsFilled += 1;
                        $this->scheduledTimeSlots[$user->userName][] = [
                            "start" => $activity->startTime,
                            "end" => $activity->endTime
                        ];

                    }
                }
            }
        }
        return $schedule;
    }
}
