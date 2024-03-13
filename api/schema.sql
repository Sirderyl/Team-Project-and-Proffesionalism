-- Schema for the database
-- Expected to be run on an empty database
-- NOTE: Test cases run this file through a very primitive parser. It will break if you put a semicolon in a string or a comment.

CREATE TABLE organization (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    admin_id INTEGER NOT NULL REFERENCES user(id)
);

CREATE TABLE activity (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    organization_id INTEGER NOT NULL REFERENCES organization(id),

    name TEXT NOT NULL,
    short_description TEXT NOT NULL,
    long_description TEXT NOT NULL,
    preview_picture BLOB,
    needed_volunteers INTEGER NOT NULL
);

CREATE TABLE activity_time (
    activity_id INTEGER NOT NULL REFERENCES activity(id),
    day_of_week TEXT NOT NULL,
    start_hour INTEGER NOT NULL,
    end_hour INTEGER NOT NULL,

    CHECK (day_of_week IN ('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')),
    CHECK (start_hour >= 0 AND start_hour < 24),
    CHECK (end_hour > 0 AND end_hour <= 24),
    CHECK (start_hour < end_hour),
    PRIMARY KEY (activity_id, day_of_week)
);

CREATE TABLE user (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE COLLATE NOCASE,
    password_hash TEXT NOT NULL,
    -- In E.164 format
    phone_number TEXT NOT NULL UNIQUE,
    profile_picture BLOB
);

CREATE TABLE user_availability (
    user_id INTEGER NOT NULL REFERENCES user(id),
    day_of_week TEXT NOT NULL,
    start_hour INTEGER NOT NULL,
    end_hour INTEGER NOT NULL,

    CHECK (day_of_week IN ('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')),
    CHECK (start_hour >= 0 AND start_hour < 24),
    CHECK (end_hour > 0 AND end_hour <= 24),
    CHECK (start_hour < end_hour),
    PRIMARY KEY (user_id, day_of_week)
);

CREATE TABLE user_organization (
    user_id INTEGER NOT NULL REFERENCES user(id),
    organization_id INTEGER NOT NULL REFERENCES organization(id),

    status TEXT NOT NULL,

    PRIMARY KEY (user_id, organization_id),
    CHECK (status IN ('Invited', 'Applied', 'Member'))
);

CREATE TABLE user_activity (
    user_id INTEGER NOT NULL REFERENCES user(id),
    activity_id INTEGER NOT NULL REFERENCES activity(id),
    start_time DATETIME NOT NULL, -- ISO 8601 format
    rating INTEGER,
    PRIMARY KEY (user_id, activity_id, start_time)
);
