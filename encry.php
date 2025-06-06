<?php
/**
 * مشفر ملفات المشروع - تشفير ملفات PHP و SQL
 * File Project Encryptor - Encrypt PHP and SQL files
 */

class FileEncryptor {
    private $key;
    private $cipher = 'AES-256-CBC';
    
    public function __construct($password = 'my_secret_key_2024') {
        // إنشاء مفتاح التشفير من كلمة المرور
        $this->key = hash('sha256', $password, true);
    }
    
    /**
     * تشفير نص
     */
    private function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * فك تشفير نص
     */
    private function decrypt($data) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
    }
    
    /**
     * تشفير ملف واحد
     */
    public function encryptFile($filePath) {
        if (!file_exists($filePath)) {
            echo "الملف غير موجود: $filePath\n";
            return false;
        }
        
        $content = file_get_contents($filePath);
        $encryptedContent = $this->encrypt($content);
        
        // حفظ الملف المشفر بامتداد .enc
        $encryptedPath = $filePath . '.enc';
        file_put_contents($encryptedPath, $encryptedContent);
        
        echo "تم تشفير: $filePath -> $encryptedPath\n";
        return true;
    }
    
    /**
     * فك تشفير ملف واحد
     */
    public function decryptFile($encryptedPath) {
        if (!file_exists($encryptedPath)) {
            echo "الملف المشفر غير موجود: $encryptedPath\n";
            return false;
        }
        
        $encryptedContent = file_get_contents($encryptedPath);
        $decryptedContent = $this->decrypt($encryptedContent);
        
        // إزالة امتداد .enc للحصول على المسار الأصلي
        $originalPath = str_replace('.enc', '', $encryptedPath);
        file_put_contents($originalPath, $decryptedContent);
        
        echo "تم فك التشفير: $encryptedPath -> $originalPath\n";
        return true;
    }
    
    /**
     * البحث عن ملفات PHP و SQL في مجلد
     */
    private function findFiles($directory, $extensions = ['php', 'sql']) {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, $extensions)) {
                    $files[] = $file->getPathname();
                }
            }
        }
        
        return $files;
    }
    
    /**
     * تشفير جميع ملفات المشروع
     */
    public function encryptProject($projectPath) {
        echo "بدء تشفير المشروع في: $projectPath\n";
        echo str_repeat('-', 50) . "\n";
        
        $files = $this->findFiles($projectPath);
        $successCount = 0;
        
        foreach ($files as $file) {
            if ($this->encryptFile($file)) {
                $successCount++;
            }
        }
        
        echo str_repeat('-', 50) . "\n";
        echo "تم تشفير $successCount من " . count($files) . " ملف\n";
    }
    
    /**
     * فك تشفير جميع ملفات المشروع
     */
    public function decryptProject($projectPath) {
        echo "بدء فك تشفير المشروع في: $projectPath\n";
        echo str_repeat('-', 50) . "\n";
        
        // البحث عن الملفات المشفرة (.enc)
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($projectPath)
        );
        
        $encryptedFiles = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'enc') {
                $encryptedFiles[] = $file->getPathname();
            }
        }
        
        $successCount = 0;
        foreach ($encryptedFiles as $file) {
            if ($this->decryptFile($file)) {
                $successCount++;
            }
        }
        
        echo str_repeat('-', 50) . "\n";
        echo "تم فك تشفير $successCount من " . count($encryptedFiles) . " ملف\n";
    }
    
    /**
     * حذف الملفات الأصلية بعد التشفير (احذر!)
     */
    public function removeOriginalFiles($projectPath) {
        $files = $this->findFiles($projectPath);
        $deletedCount = 0;
        
        foreach ($files as $file) {
            // التأكد من وجود النسخة المشفرة
            if (file_exists($file . '.enc')) {
                unlink($file);
                $deletedCount++;
                echo "تم حذف الملف الأصلي: $file\n";
            }
        }
        
        echo "تم حذف $deletedCount ملف أصلي\n";
    }
    
    /**
     * حذف الملفات المشفرة بعد فك التشفير
     */
    public function removeEncryptedFiles($projectPath) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($projectPath)
        );
        
        $deletedCount = 0;
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'enc') {
                unlink($file->getPathname());
                $deletedCount++;
                echo "تم حذف الملف المشفر: " . $file->getPathname() . "\n";
            }
        }
        
        echo "تم حذف $deletedCount ملف مشفر\n";
    }
}

// مثال على الاستخدام
try {
    // إنشاء كائن المشفر
    $encryptor = new FileEncryptor('كلمة_مرور_قوية_2024');
    
    // مسار المشروع
    $projectPath = './my_project'; // غير هذا إلى مسار مشروعك
    
    // التشفير
    echo "=== تشفير المشروع ===\n";
    $encryptor->encryptProject($projectPath);
    
    // يمكنك حذف الملفات الأصلية (احذر!)
    // $encryptor->removeOriginalFiles($projectPath);
    
    echo "\n=== فك التشفير ===\n";
    // فك التشفير
    $encryptor->decryptProject($projectPath);
    
    // حذف الملفات المشفرة بعد فك التشفير
    // $encryptor->removeEncryptedFiles($projectPath);
    
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
}

/*
تعليمات الاستخدام:

1. غير متغير $projectPath إلى مسار مشروعك
2. غير كلمة المرور في الكونستركتور
3. تشغيل الكود: php file_encryptor.php

ملاحظات مهمة:
- احتفظ بنسخة احتياطية قبل التشفير
- لا تفقد كلمة المرور وإلا ستفقد ملفاتك
- الكود يشفر ملفات PHP و SQL فقط
- الملفات المشفرة تحفظ بامتداد .enc

وظائف متقدمة:
- encryptFile(): تشفير ملف واحد
- decryptFile(): فك تشفير ملف واحد
- encryptProject(): تشفير كامل المشروع
- decryptProject(): فك تشفير كامل المشروع
- removeOriginalFiles(): حذف الملفات الأصلية (خطر!)
- removeEncryptedFiles(): حذف الملفات المشفرة
*/
?>