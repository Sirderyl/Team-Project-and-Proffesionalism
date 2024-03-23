<?php

declare(strict_types=1);

namespace App;

class Scheduler
{
    public array $users = [];
    public array $activities = [];
    public array $ratings = [];
    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database)
    {
        $this->database = $database;
        $this->users = $this->database->users()->getAll();
        $this->ratings = $this->database->activities()->getAllUserRatings();
        $this->activities = $this->database->activities()->getAll();
    }
    public function getRecommendedActivities(int $userId)
    {
        $schedule = [];
        $scheduledTimeSlots = [];
        $organizationRatings = $this->getOrganizationRatings();

        foreach ($this->activities as $activity) {
        foreach ($activity->times as $day => $timeRange) {


            $activityDayOfWeek = $day;
            $volunteerSlotsFilled = 0;

            foreach ($this->users as $user) {
                if(isset($organizationRatings[$user->userId]))
                {
                $userOrganizationRatings = $organizationRatings[$user->userId]["organizations"];
                }
                $associatedOrgRating = NULL;
                $activityRating = NULL;

                foreach ($this->ratings as $rating) {
                    if (($rating->userId === $user->userId) && ($rating->activityId === $activity->id)) {
                        $activityRating = $rating->rating;
                        break; 
                    }
                }

                foreach ($userOrganizationRatings as $userOrganizationRating) {
                    if ($userOrganizationRating["organizationId"] == $activity->organizationId)
                        $associatedOrgRating = $userOrganizationRating["rating"];
                    break;
                }

                if (isset ($user->availability[$activityDayOfWeek]) && $user->availability[$activityDayOfWeek] !== null) {
                    $userAvailableStart = $user->availability[$activityDayOfWeek]->start;
                    $userAvailableEnd = $user->availability[$activityDayOfWeek]->end;
                    if(isset($activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->start)){
                    $activityStart = $activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->start;
                    }
                    if(isset($activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->end)){
                    $activityEnd = $activity->getTime(DayOfWeek::fromString($activityDayOfWeek))->end;
                    }

                    $isUserAvailable = true;
                    if (isset ($scheduledTimeSlots[$user->userId])) {
                        foreach ($scheduledTimeSlots[$user->userId] as $timeSlot) {
                            if (($timeSlot["start"] < $activityEnd) && ($timeSlot["end"] > $activityStart) && ($activityDayOfWeek == $timeSlot["day"])) {
                                $isUserAvailable = false;
                                break;
                            }
                        }
                    }

                    if ($isUserAvailable && ($activityStart < $userAvailableEnd) && ($activityEnd > $userAvailableStart) && ($volunteerSlotsFilled < $activity->neededVolunteers)) {
                        //if(($associatedOrgRating > 2.5) || ($activityRating >= 4))
                        //{
                        if (!isset ($schedule[$activity->id])) {
                            $schedule[$activity->id]["day"] =
                                [
                                    'activityId' => $activity->id,
                                    'activityName' => $activity->name,
                                    'start' => $activityStart,
                                    'end' => $activityEnd,
                                    'day' => $activityDayOfWeek,
                                    'users' => [
                                    ['userId' => $user->userId,
                                    'userName' => $user->userName]
                                    ]
                                ];

                        }

                        $volunteerSlotsFilled += 1;
                        $scheduledTimeSlots[$user->userId][$activityDayOfWeek] = [
                            "activity" => $activity->name,
                            "start" => $activityStart,
                            "end" => $activityEnd,
                            "day" => $activityDayOfWeek
                        ];
                        //}
                    }
                }
            }
        }    
        }

        $recommendedActivities = array_filter($schedule, function($activity) use ($userId) {
            foreach ($activity['day']['users'] as $user) {
                if ($user['userId'] == $userId) {
                    return true;
                }
            }
            return false;
        });
        return $recommendedActivities;
    }

    public function getOrganizationRatings(): array
    {
        $ratings = $this->ratings;
        $activities = $this->activities;

        // Add OrganizationId to Ratings
        foreach ($ratings as $rating) {
            $organizationId = 0;

            foreach ($activities as $activity) {
                if ($activity->id == $rating->activityId) {
                    $organizationId = $activity->organizationId;
                    $rating->organizationId = $organizationId;
                    break;
                }
            }
        }

        // Split Ratings into Separate Arrays for each user
        $ratingsByUser = [];
        foreach ($ratings as $rating) {
            $userId = $rating->userId;
            if (!isset ($ratingsByUser[$userId])) {
                $ratingsByUser[$userId] = [];
            }
            $ratingsByUser[$userId][] = $rating;
        }

        //Calculate rating sum and count of each organization for each user
        $organizationSumCount = [];
        foreach ($ratingsByUser as $user) {
            foreach ($user as $rating) {
                $userId = $rating->userId;
                if (!isset ($organizationSumCount[$userId][$rating->organizationId])) {
                    $organizationSumCount[$userId][$rating->organizationId]['sum'] = 0;
                    $organizationSumCount[$userId][$rating->organizationId]['count'] = 0;
                }
                $organizationSumCount[$userId][$rating->organizationId]['sum'] += $rating->rating;
                $organizationSumCount[$userId][$rating->organizationId]['count']++;
            }
        }

        //Calculate rating average for each organization for each user
        $result = [];
        foreach ($organizationSumCount as $userId => $organizations) {
            $userRatings = [];
            foreach ($organizations as $organizationId => $data) {
                $userRatings[] = [
                    "organizationId" => $organizationId,
                    "rating" => $data['sum'] / $data['count']
                ];
            }
            $result[$userId] = [
                "userId" => $userId,
                "organizations" => $userRatings
            ];
        }

        return $result;
    }
}