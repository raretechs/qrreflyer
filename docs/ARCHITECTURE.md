# Architecture

QRReflyer is evolving into Castra Realty Marketing Studio.

## Goals

- Keep production code organized and maintainable.
- Separate application logic from public files and runtime storage.
- Use SQLite for internal application data.
- Support future modules beyond flyers.

## Structure

- `app/` - application code
- `admin/` - admin interface
- `public/` - browser-facing entry point and assets
- `storage/` - uploads, temp files, logs
- `database/` - migrations and seed data
- `templates/` - flyer templates

## v1.1 Priorities

1. Configuration layer
2. SQLite database
3. Agent admin
4. ZIP upload
5. Image picker
