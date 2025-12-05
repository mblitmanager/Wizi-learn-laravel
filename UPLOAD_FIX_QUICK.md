# Fix Laravel Upload Production - Solution Rapide

## 🎯 Configuration Production Immédiate

### 1. Modifier `.env` (PRIORITÉ)

```env
# Modifier ces valeurs
UPLOAD_MAX_FILESIZE=100M
POST_MAX_SIZE=100M
MEMORY_LIMIT=512M
```

### 2. Créer `public/.htaccess` (si manquant)

```apache
<IfModule mod_php.c>
    php_value upload_max_filesize 100M
    php_value post_max_size 100M
    php_value max_execution_time 300
    php_value memory_limit 512M
</IfModule>
```

### 3. Vérifier Permissions Storage

```bash
# SSH dans votre serveur
cd /path/to/your/laravel

# Permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public/uploads

# Owner (remplacer www-data si besoin)
chown -R www-data:www-data storage
chown -R www-data:www-data public/uploads
```

### 4. Test de Configuration

Créez `public/upload-test.php`:

```php
<?php
$limits = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
];

header('Content-Type: application/json');
echo json_encode($limits, JSON_PRETTY_PRINT);
```

Visitez: `https://votre-domaine.com/upload-test.php`

**Résultat attendu:**

```json
{
    "upload_max_filesize": "100M",
    "post_max_size": "100M",
    "memory_limit": "512M",
    "max_execution_time": "300"
}
```

### 5. Ajouter Logging au Controller

Modifiez `uploadVideo()` dans `MediaController.php`:

```php
public function uploadVideo(\App\Http\Requests\VideoUploadRequest $request)
{
    // ADD LOGGING
    \Log::info('Upload attempt', [
        'has_file' => $request->hasFile('video'),
        'file_size' => $request->file('video')?->getSize() / 1024 / 1024 . ' MB',
        'max_upload' => ini_get('upload_max_filesize'),
        'max_post' => ini_get('post_max_size'),
    ]);

    try {
        // ... reste du code
    } catch (\Exception $e) {
        \Log::error('Upload failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
```

### 6. Tester l'Upload

```bash
# Logs en temps réel
tail -f storage/logs/laravel.log
```

Testez upload depuis frontend, vérifiez les logs.

---

## ✅ Checklist Rapide

- [ ] `.env` OK (upload_max, post_max, memory)
- [ ] `.htaccess` créé avec valeurs PHP
- [ ] Permissions 775 sur storage/public/uploads
- [ ] Owner www-data sur storage/uploads
- [ ] Test upload-test.php affiche bonnes valeurs
- [ ] Logging ajouté au controller
- [ ] Test upload réel avec logs

---

**Temps:** 10 minutes  
**Redémarrage requis:** Oui (Apache/Nginx + PHP)

Après ces étapes, l'upload devrait fonctionner!
