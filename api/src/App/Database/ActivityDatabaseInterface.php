<?php

namespace App\Database;

/**
 * Database interface for activities
 * @author Kieran
 */
interface ActivityDatabaseInterface {
    public function get(string $id): \App\Activity;
    public function create(\App\Activity $activity): void;

    public function setPreviewPicture(string $activityId, string $image): void;
    public function getPreviewPicture(string $activityId): string;
}
