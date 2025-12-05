# Fix Upload Média Production Laravel

## 🔴 Erreur: `validation.uploaded`

Cette erreur indique que le fichier n'a pas pu être uploadé. En production, c'est souvent lié aux **limites PHP** ou aux **permissions de dossiers**.

---

## 🔧 Solution 1: Augmenter les Limites PHP

### Fichier: `php.ini` (Production)

Localisez votre `php.ini`:

```bash
php --ini
```

Modifiez ces valeurs:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

**Redémarrer PHP-FPM:**

```bash
# Ubuntu/Debian
sudo systemctl restart php8.1-fpm

# CentOS/RHEL
sudo systemctl restart php-fpm
```

---

## 🔧 Solution 2: Configuration Nginx (si applicable)

### Fichier: `/etc/nginx/sites-available/votre-site`

```nginx
server {
    # ... autres configurations
    
    client_max_body_size 100M;
    client_body_timeout 300s;
    
    location ~ \.php$ {
        # ... autres configurations
        
        fastcgi_read_timeout 300;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }
}
```

**Redémarrer Nginx:**

```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## 🔧 Solution 3: Permissions Dossiers Laravel

```bash
# Depuis le dossier Laravel
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Créer le dossier media si manquant
mkdir -p storage/app/public/media
sudo chown -R www-data:www-data storage/app/public/media
sudo chmod -R 775 storage/app/public/media

# Vérifier le symbolic link
php artisan storage:link
```

---

## 🔧 Solution 4: Validation Laravel - Ajouter Logging

### Modifier le Controller d'Upload

Ajoutez du debugging pour voir l'erreur exacte:

```php
public function uploadMedia(Request $request)
{
    // Log pour debugging
    \Log::info('Upload attempt', [
        'file_present' => $request->hasFile('media'),
        'file_size' => $request->file('media')?->getSize(),
        'php_upload_max' => ini_get('upload_max_filesize'),
        'php_post_max' => ini_get('post_max_size'),
    ]);

    try {
        $validator = Validator::make($request->all(), [
            'media' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif,mp4,mov,avi,pdf,doc,docx',
                'max:102400' // 100MB en KB
            ],
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            return response()->json([
                'error' => 'Validation échouée',
                'details' => $validator->errors()
            ], 422);
        }

        // Upload logic...
        
    } catch (\Exception $e) {
        \Log::error('Upload exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Erreur serveur',
            'message' => $e->getMessage()
        ], 500);
    }
}
```

---

## 🔧 Solution 5: Vérifier Dossier Temp PHP

```bash
# Vérifier le dossier temp
php -i | grep upload_tmp_dir

# Si vide ou non accessible, créer et configurer
sudo mkdir -p /var/www/tmp
sudo chown www-data:www-data /var/www/tmp
sudo chmod 1777 /var/www/tmp
```

Dans `php.ini`:

```ini
upload_tmp_dir = /var/www/tmp
```

---

## 🔧 Solution 6: .htaccess (Apache)

Si vous utilisez Apache, ajoutez à `.htaccess`:

```apache
php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value max_execution_time 300
php_value max_input_time 300
```

---

## 🧪 Test de Configuration

### Script de Test PHP

Créez `public/phpinfo-test.php`:

```php
<?php
echo "Upload Max: " . ini_get('upload_max_filesize') . "<br>";
echo "Post Max: " . ini_get('post_max_size') . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";
echo "Temp Dir: " . ini_get('upload_tmp_dir') . "<br>";
echo "Disk Space: " . disk_free_space('/') / 1024 / 1024 / 1024 . " GB<br>";

// Test write permission
$testFile = storage_path('app/test.txt');
if (file_put_contents($testFile, 'test')) {
    echo "Storage writable: ✅<br>";
    unlink($testFile);
} else {
    echo "Storage writable: ❌<br>";
}
```

Accédez: `https://votre-domaine.com/phpinfo-test.php`

**⚠️ SUPPRIMEZ ce fichier après le test!**

---

## 🧪 Test d'Upload via Artisan

```bash
php artisan tinker
```

```php
// Test upload simulation
$file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg', 1000, 1000); // 1000x1000
Storage::disk('public')->put('test.jpg', file_get_contents($file));
echo "Test upload success!\n";
```

---

## 📊 Checklist de Diagnostic

- [ ] `php.ini` - upload_max_filesize ≥ 100M
- [ ] `php.ini` - post_max_size ≥ 100M
- [ ] `php.ini` - memory_limit ≥ 256M
- [ ] Nginx - client_max_body_size ≥ 100M
- [ ] Permissions storage: 775
- [ ] Owner storage: www-data
- [ ] `php artisan storage:link` exécuté
- [ ] Dossier temp accessible
- [ ] Logs Laravel: `storage/logs/laravel.log`
- [ ] Espace disque suffisant

---

## 🔍 Vérifier les Logs

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs Nginx
tail -f /var/log/nginx/error.log

# Logs PHP
tail -f /var/log/php8.1-fpm.log
```

---

## 💡 Messages d'Erreur Courants

### "The uploaded file exceeds the upload_max_filesize directive"

→ Augmenter `upload_max_filesize` dans php.ini

### "Maximum execution time exceeded"

→ Augmenter `max_execution_time` dans php.ini

### "Allowed memory size exhausted"

→ Augmenter `memory_limit` dans php.ini

### "Failed to open stream: Permission denied"

→ Corriger permissions avec `chmod 775 storage`

### "413 Request Entity Too Large"

→ Augmenter `client_max_body_size` dans Nginx

---

## 🚀 Configuration Production Recommandée

### php.ini

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
upload_tmp_dir = /var/www/tmp
```

### nginx.conf

```nginx
client_max_body_size 100M;
client_body_timeout 300s;
fastcgi_read_timeout 300;
```

### Laravel .env

```env
FILESYSTEM_DISK=public
```

---

## 📝 Après Correction

1. Redémarrer services:

```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

2. Tester upload depuis frontend

3. Vérifier logs:

```bash
tail -f storage/logs/laravel.log
```

4. Si toujours erreur, ajouter logging détaillé au controller

---

**Temps estimé:** 15-30 minutes  
**Complexité:** ⭐⭐⭐
