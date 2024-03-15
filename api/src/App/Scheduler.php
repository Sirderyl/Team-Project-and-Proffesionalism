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
    public array $ratings = [];

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
        $this->activityOne->organizationId = 4;
        $this->activityOne->setTime(DayOfWeek::Wednesday, new TimeRange(12.00, 13.00));

        $this->activityTwo->id = 2;
        $this->activityTwo->name = "Walking Dogs";
        $this->activityTwo->neededVolunteers = 1;
        $this->activityTwo->organizationId = 4;
        $this->activityTwo->setTime(DayOfWeek::Tuesday, new TimeRange(12.00, 13.00));

        $this->activityThree->id = 3;
        $this->activityThree->name = "Answering Calls";
        $this->activityThree->neededVolunteers = 3;
        $this->activityThree->organizationId = 2;
        $this->activityThree->setTime(DayOfWeek::Monday, new TimeRange(12.00, 13.00));

        $this->activityFour->id = 4;
        $this->activityFour->name = "Cleaning";
        $this->activityFour->neededVolunteers = 3;
        $this->activityFour->organizationId = 7;
        $this->activityFour->setTime(DayOfWeek::Monday, new TimeRange(12.00, 13.00));

        $this->activities = [$this->activityOne, $this->activityTwo, $this->activityThree, $this->activityFour];
        $this->ratings = array(
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
        $userName = "";
        foreach ($this->activities as $activity) {
            $activityDayOfWeek = array_keys($activity->times)[0];
            $volunteerSlotsFilled = 0;

            foreach ($this->users as $user) {

                if ($user->userId == $userId) {
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
        $scheduledTimeSlots = [];
        foreach ($this->activities as $activity) {
            $activityDayOfWeek = array_keys($activity->times)[0];
            $volunteerSlotsFilled = 0;

            foreach ($this->users as $user) {

                if (isset($user->availability[$activityDayOfWeek]) && $user->availability[$activityDayOfWeek] !== null) {
                    $userAvailableStart = $user->availability[$activityDayOfWeek]->start;
                    $userAvailableEnd = $user->availability[$activityDayOfWeek]->end;

                    $activityStart = $activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->start;
                    $activityEnd = $activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->end;

                    $isUserAvailable = true;
                    if (isset($scheduledTimeSlots[$user->userId])) {
                        foreach ($scheduledTimeSlots[$user->userId] as $timeSlot) {
                            if (($timeSlot["start"] < $activityEnd) && ($timeSlot["end"] > $activityStart) && ($activityDayOfWeek == $timeSlot["day"])) {
                                $isUserAvailable = false;
                                break;
                            }
                        }
                    }

                    if ($isUserAvailable && ($activityStart < $userAvailableEnd) && ($activityEnd > $userAvailableStart) && ($volunteerSlotsFilled < $activity->neededVolunteers)) {
                        if (!isset($schedule[$activity->id])) {
                            $schedule[$activity->id]["details"] =
                                [
                                    'activityName' => $activity->name,
                                    'start' => $activityStart,
                                    'end' => $activityEnd,
                                    'day' => $activityDayOfWeek
                                ];

                        }

                        $schedule[$activity->id]["users"][] =
                            [
                                'userId' => $user->userId,
                                'userName' => $user->userName
                            ];


                        $volunteerSlotsFilled += 1;
                        $scheduledTimeSlots[$user->userId][] = [
                            "activity" => $activity->name,
                            "start" => $activityStart,
                            "end" => $activityEnd,
                            "day" => $activityDayOfWeek
                        ];

                    }
                }
            }
        }
        return $schedule;
    }

    public function getOrganizationRatings(/*array $ratings, array $activities*/): array {
        $ratings = $this->ratings;
        $activities = $this->activities;

        // Add OrganizationId to Ratings
        foreach($ratings as $rating)
        {
            $organizationId = 0;

            foreach($activities as $activity)
            {
                if($activity->id == $rating->activityId)
                {
                    $organizationId = $activity->organizationId;
                    $rating->organizationId = $organizationId;
                    break;
                }
            }
        }

        // Split Ratings into Separate Arrays for each user
        $ratingsByUser = [];
        foreach($ratings as $rating)
        {
            $userId = $rating->userId;
            if (!isset($ratingsByUser[$userId])) {
                $ratingsByUser[$userId] = [];
            }
            $ratingsByUser[$userId][] = $rating;
        }

        //Calculate rating sum and count of each organization for each user
        $organizationSumCount = [];
        foreach($ratingsByUser as $user)
        {
            foreach($user as $rating)
            {
                $userId = $rating->userId;
                if (!isset($organizationSumCount[$userId][$rating->organizationId])) {
                    $organizationSumCount[$userId][$rating->organizationId]['sum'] = 0;
                    $organizationSumCount[$userId][$rating->organizationId]['count'] = 0;
                }
                $organizationSumCount[$userId][$rating->organizationId]['sum'] += $rating->rating;
                $organizationSumCount[$userId][$rating->organizationId]['count']++;
            }
        }

        //Calculate rating average for each organization for each user
        $averageRatings = [];

        foreach ($organizationSumCount as $userId => $organizations) {
            foreach ($organizations as $organizationId => $data) {
                $sum = $data['sum'];
                $count = $data['count'];
            
                $averageRating = $count > 0 ? $sum / $count : 0;

                $averageRatings[$userId][$organizationId] = $averageRating;
            }
        }

        return $averageRatings;
    }
}
