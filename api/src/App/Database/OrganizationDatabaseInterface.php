<?php

namespace App\Database;

interface OrganizationDatabaseInterface {
    public function get(int $id): \App\Organization;

    public function create(\App\Organization $organization): void;

    public function setUserStatus(int $organizationId, int $userId, \App\UserOrganizationStatus $status): void;

    public function getUserStatus(int $organizationId, int $userId): \App\UserOrganizationStatus;
}
