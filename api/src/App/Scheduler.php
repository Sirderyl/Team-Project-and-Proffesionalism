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