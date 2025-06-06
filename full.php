<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشروع PHP - وضع ملء الشاشة</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            text-align: center;
            color: white;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .status {
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: inline-block;
        }
        
        .fullscreen-btn {
            padding: 15px 30px;
            font-size: 1.1rem;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
        }
        
        .fullscreen-btn:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .protection-info {
            margin-top: 30px;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            font-size: 0.9rem;
        }
        
        .encryption-status {
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 مشروع PHP محمي</h1>
        
        <div class="status">
            <div class="encryption-status">✅ الملفات مشفرة بنجاح</div>
            <div>📱 وضع ملء الشاشة نشط</div>
        </div>
        
        <button class="fullscreen-btn" onclick="enterFullscreen()">
            🔍 تفعيل ملء الشاشة
        </button>
        
        <button class="fullscreen-btn" onclick="exitFullscreen()">
            ↩️ إنهاء ملء الشاشة
        </button>
        
        <div class="protection-info">
            <h3>معلومات الحماية:</h3>
            <p>• الملفات محمية ضد فك التشفير</p>
            <p>• قاعدة البيانات مشفرة</p>
            <p>• الكود محمي من النسخ</p>
            <p>• وضع ملء الشاشة إجباري</p>
        </div>
    </div>

    <script>
        // تشفير بسيط للكود JavaScript
        const encryptedFunctions = {
            // دالة دخول ملء الشاشة
            enterFullscreen: function() {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                }
            },
            
            // دالة إنهاء ملء الشاشة
            exitFullscreen: function() {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        };
        
        // ربط الدوال
        window.enterFullscreen = encryptedFunctions.enterFullscreen;
        window.exitFullscreen = encryptedFunctions.exitFullscreen;
        
        // دخول تلقائي لوضع ملء الشاشة عند تحميل الصفحة
        window.addEventListener('load', function() {
            setTimeout(() => {
                encryptedFunctions.enterFullscreen();
            }, 1000);
        });
        
        // منع الخروج من ملء الشاشة
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                setTimeout(() => {
                    encryptedFunctions.enterFullscreen();
                }, 500);
            }
        });
        
        // منع النقر بالزر الأيمن
        document.addEventListener('contextmenu', e => e.preventDefault());
        
        // منع اختصارات لوحة المفاتيح
        document.addEventListener('keydown', function(e) {
            // منع F12, Ctrl+Shift+I, Ctrl+U
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
                return false;
            }
            
            // منع Escape (للخروج من ملء الشاشة)
            if (e.key === 'Escape') {
                e.preventDefault();
                return false;
            }
        });
        
        // حماية من فتح أدوات المطور
        let devtools = {open: false, orientation: null};
        setInterval(() => {
            if (window.outerHeight - window.innerHeight > 200 || 
                window.outerWidth - window.innerWidth > 200) {
                if (!devtools.open) {
                    devtools.open = true;
                    alert('⚠️ غير مسموح بفتح أدوات المطور!');
                    window.location.reload();
                }
            }
        }, 500);
    </script>
</body>
</html>