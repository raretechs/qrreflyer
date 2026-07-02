CREATE TABLE IF NOT EXISTS agents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    slug TEXT NOT NULL UNIQUE,
    display_name TEXT NOT NULL,

    first_name TEXT,
    last_name TEXT,

    email TEXT,
    phone TEXT,
    mobile TEXT,

    dre_license TEXT,

    website TEXT,

    photo_path TEXT,

    bio TEXT,

    facebook TEXT,
    instagram TEXT,
    linkedin TEXT,
    youtube TEXT,

    active INTEGER NOT NULL DEFAULT 1,

    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
