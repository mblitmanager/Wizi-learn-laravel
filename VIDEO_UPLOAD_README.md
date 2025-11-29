Backend video upload

Overview
- The Laravel API already exposes POST `/api/medias/upload-video` (authenticated) handled by `MediaController::uploadVideo`.
- Uploaded files are stored using the `public` disk under `storage/app/public/videos` and the returned `media.url` uses `asset('storage/...')`.

Required server steps
1. Run migrations if you add the optional Media model migration (not required for file storage only).
2. Ensure the `public/storage` symlink exists so uploaded files are publicly accessible:

```bash
php artisan storage:link
```

3. Ensure `FILESYSTEM_DRIVER=public` or disk `public` configured in `config/filesystems.php`.
4. Adjust `upload_max_filesize` and `post_max_size` in `php.ini` to allow large uploads (e.g., 500M).

Client usage (React)
- POST multipart/form-data to `/api/medias/upload-video` with field `video` and optional `titre`, `description`, `formation_id`, `categorie`, `ordre`.
- Include Authorization header: `Bearer <token>`.
- The endpoint returns `{ success: true, media }` where `media.url` is the public URL to the uploaded file.

Streaming
- A streaming endpoint exists at `/api/media/stream/{path}` which reads files from `public` and supports Range requests for seeking.

Notes
- The upload request uses `App\Http\Requests\VideoUploadRequest` for validation (mimetypes and max size).
- If you prefer storing videos directly in `public/videos`, you can change storage to store there and update `media.url` accordingly.

Security
- Protect upload routes as needed and sanitize filenames. Current implementation uses a timestamp prefix to avoid collisions.

