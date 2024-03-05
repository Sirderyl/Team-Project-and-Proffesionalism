<?php

namespace App;

/**
 * Implementation of /activity/{id}/previewimage
 */
class ActivityPicture {
    private Database\DatabaseInterface $database;

    public function __construct(Database\DatabaseInterface $database) {
        $this->database = $database;
    }

    /**
     * Get the raw JPEG data for an activity's preview picture
     */
    public function execute(string $id): string {
        return $this->database->activities()->getPreviewPicture($id);
    }

    public function getContentType(): string {
        return 'image/jpeg';
    }
}
