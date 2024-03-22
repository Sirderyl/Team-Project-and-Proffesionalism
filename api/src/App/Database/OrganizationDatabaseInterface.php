<?php

namespace App\Database;

interface OrganizationDatabaseInterface {
    public function get(int $id): \App\Organization;

    public function create(\App\Organization $organization): void;

    public function setUserStatus(int $organizationId, int $userId, \App\UserOrganizationStatus $status): void;

    public function getUserStatus(int $organizationId, int $userId): \App\UserOrganizationStatus;

    /**
     * Get the organization's activity schedule for a given date range.
     * @return array<array{
     *   activity: array{
     *      id: int,
     *      name: string,
     *      shortDescription: string,
     *   },
     *   startTime: \DateTime,
     *   users: array<array{
     *     id: int,
     *     name: string,
     *   }>
     * }>
     */
    public function getActivitySchedule(int $organizationId, \DateTime $from = null, \DateTime $to = null): array;
}
