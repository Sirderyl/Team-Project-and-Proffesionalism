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

        $this->activityOne = new Activity();
        $this->activityTwo = new Activity();
        $this->activityThree = new Activity();
        $this->activityFour = new Activity();

        $this->activityOne->id = 1;
        $this->activityOne->name = "Serving Food";
        $this->activityOne->neededVolunteers = 2;
        $this->activityOne->setTime(DayOfWeek::Wednesday, new TimeRange(12.00,13.00));

        $this->activityTwo->id = 2;
        $this->activityTwo->name = "Walking Dogs";
        $this->activityTwo->neededVolunteers = 1;
        $this->activityTwo->setTime(DayOfWeek::Tuesday, new TimeRange(12.00,13.00));

        $this->activityThree->id = 3;
        $this->activityThree->name = "Answering Calls";
        $this->activityThree->neededVolunteers = 3;
        $this->activityThree->setTime(DayOfWeek::Monday, new TimeRange(12.00,13.00));

        $this->activityFour->id = 4;
        $this->activityFour->name = "Cleaning";
        $this->activityFour->neededVolunteers = 3;
        $this->activityFour->setTime(DayOfWeek::Monday, new TimeRange(12.00,13.00));

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

    public function getUserSchedule(int $userId)
    {
        $schedule = [];
        $username = "";
        foreach ($this->activities as $activity) {
            $activityDayOfWeek = array_keys($activity->times)[0];
            $volunteerSlotsFilled = 0;

            foreach ($this->users as $user) {

                if($user->userId == $userId)
                {
                    $userName = $user->userName;
                }

                if (isset($user->availability[$activityDayOfWeek]) && $user->availability[$activityDayOfWeek] !== null) {
                    $userAvailableStart = $user->availability[$activityDayOfWeek]->start;
                    $userAvailableEnd = $user->availability[$activityDayOfWeek]->end;

                    $activityStart = $activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->start;
                    $activityEnd = $activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->end;

                    $isUserAvailable = true;
                    
                   if (isset($schedule[$user->userId])) {
                        foreach ($schedule[$user->userId] as $timeSlot) {
                            if (($timeSlot["start"] <= $activityEnd) && ($timeSlot["end"] >= $activityStart) && ($activityDayOfWeek == $timeSlot["day"])) {
                                $isUserAvailable = false;
                                break;
                            }
                        }
                    }

                    if ($isUserAvailable && ($activityStart <= $userAvailableEnd) && ($activityEnd >= $userAvailableStart) && ($volunteerSlotsFilled < $activity->neededVolunteers)) {

                        $volunteerSlotsFilled += 1;
                        $schedule[$user->userId][] = [
                            "activity" => $activity->name,
                            "start" => $activityStart,
                            "end" => $activityEnd,
                            "day" => $activityDayOfWeek
                        ];

                    }
                }
            }
        }
       return [
            "userId" => $userId,
            "userName" => $userName,
            "schedule" => $schedule[$userId]
       ];

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

                    $isUserAvailable = true;
                    if (isset($this->scheduledTimeSlots[$user->userName])) {
                        foreach ($this->scheduledTimeSlots[$user->userName] as $timeSlot) {
                            if (($timeSlot["start"] < $activity->endTime) && ($timeSlot["end"] > $activity->startTime)) {
                                $isUserAvailable = false;
                                break;
                            }
                        }
                    }

                    if ($isUserAvailable && ($activityStart < $userAvailableEnd) && ($activityEnd > $userAvailableStart) && ($volunteerSlotsFilled < $activity->neededVolunteers)) {

                        $schedule[$activity->name][$activity->startTime->format('Y-m-d H:i')][$activity->endTime->format('Y-m-d H:i')][] =
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
