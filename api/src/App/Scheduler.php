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
    $organizationRatings = $this->getOrganizationRatings();
    $activitiesSchedule = [];
    $usersOccupiedTimes = [];
    
    foreach ($this->activities as $activity) {
        $volunteerSlotsFilled = 0;
        
        foreach ($activity->times as $activityDay => $activityTimeRange) {
            
            foreach ($this->users as $user) {
                if(isset($organizationRatings[$user->userId]))
                {
                $userOrganizationRatings = $organizationRatings[$user->userId]["organizations"];
                }
                $activityRating = null;
                $associatedOrgRating = null;

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

                foreach ($user->availability as $userDay => $userTimeRange) {
                    $overlap = false;
                    
                    if (isset($usersOccupiedTimes[$user->userId][$activityDay])) {
                        foreach ($usersOccupiedTimes[$user->userId][$activityDay] as $occupiedTime) {
                            if (($activityTimeRange->start >= $occupiedTime["start"] && $activityTimeRange->start < $occupiedTime["end"]) || 
                                ($activityTimeRange->end > $occupiedTime["start"] && $activityTimeRange->end <= $occupiedTime["end"]) ||
                                ($activityTimeRange->start <= $occupiedTime["start"] && $activityTimeRange->end >= $occupiedTime["end"])) {
                                $overlap = true;
                                break;
                            }
                        }
                    }
                    
                    if (($userTimeRange->start <= $activityTimeRange->start)
                        && ($userTimeRange->end >= $activityTimeRange->end)
                        && ($activityDay == $userDay)
                        && ($volunteerSlotsFilled < $activity->neededVolunteers)
                        && !$overlap) {
                        
                        if(($activityRating >=4 || $associatedOrgRating > 2.5) || ($associatedOrgRating == NULL))
                         {
                        
                            $activitiesSchedule[$activity->id][$activityDay][] = [
                                'activityId' => $activity->id,
                                'activityName' => $activity->name,
                                'activityDay' => $activityDay,
                                'activityStart' => $activityTimeRange->start,
                                'activityEnd' => $activityTimeRange->end,
                                'userId' => $user->userId,
                                'userName' => $user->userName
                            ];
                            
                            $usersOccupiedTimes[$user->userId][$userDay][] = [
                                'start' => $activityTimeRange->start,
                                'end' => $activityTimeRange->end
                            ];
                            
                            $volunteerSlotsFilled++;
                        }
                    }
                }
            }
        }
    }
    
    $userActivities = [];
    foreach ($activitiesSchedule as $activity) {
        foreach ($activity as $day => $details) {
            foreach ($details as $detail) {
                if ($detail['userId'] == $userId) {
                    $userActivities[] = $detail;
                }
            }
        }
    }
    
    return $userActivities;
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