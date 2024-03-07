<?php

namespace App\Database;

/**
 * Database interface for activities
 * @author Kieran
 */
interface ActivityDatabaseInterface {
    public function get(int $id): \App\Activity;
    public function create(\App\Activity $activity): void;

    public function setPreviewPicture(int $activityId, string $image): void;
    public function getPreviewPicture(int $activityId): string;
}
