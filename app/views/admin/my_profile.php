<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân | PhươngSTORE Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/admin.css">
    <style>
        .profile-container {
            text-align: center;
            max-width: 500px;
            margin: 50px auto;
            background: #1a1a1a;
            padding: 40px;
            border-radius: 15px;
            border: 1px solid #333;
        }
        .profile-avatar {
            width: 180px; height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--gold-color);
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        .profile-avatar:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
        .camera-icon {
            color: #ccc; font-size: 14px; margin-top: 10px;
        }
        .info-box {
            margin-top: 30px;
            text-align: left;
            background: #252525;
            padding: 20px;
            border-radius: 8px;
        }
        .info-row {
            display: flex; justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #333;
            color: #ddd;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: bold; color: #888; }
    </style>
</head>
<body class="admin-body">

<?php include 'sidebar.php'; ?>

<?php if (isset($_SESSION['admin_toast'])): ?>
    <div id="toast-notification" class="toast-msg">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= $_SESSION['admin_toast'] ?></span>
    </div>
    <?php unset($_SESSION['admin_toast']); ?>
    <script>setTimeout(() => { document.getElementById('toast-notification')?.remove(); }, 3000);</script>
<?php endif; ?>

<main class="main-content-admin">
    <section class="admin-section active">
        <h1>Hồ sơ của bạn</h1>

        <div class="profile-container">
            <form id="avatarForm" action="index.php?ctrl=admin&act=my_profile" method="POST" enctype="multipart/form-data">
                <input type="file" name="avatar" id="avatarInput" style="display: none;" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                
                <label for="avatarInput" title="Bấm để đổi ảnh đại diện">
                    <?php 
                        $img = !empty($myInfo['anh_admin']) ? $myInfo['anh_admin'] : 'default_admin.png';
                        // Logic tìm ảnh giống bên staff
                        $src = "public/images/$img";
                        if(!file_exists($src)) {
                            $src = (file_exists("public/images/$img")) ? "public/images/$img" : "public/images/";
                        }
                    ?>
                    <img src="<?= $src ?>" class="profile-avatar">
                    <div class="camera-icon"><i class="fa-solid fa-camera"></i> Bấm vào ảnh để thay đổi</div>
                </label>
            </form>

            <h2 style="color: white; margin-top: 20px;"><?= htmlspecialchars($myInfo['ho_ten']) ?></h2>
            <div style="color: var(--gold-color); font-weight: bold;">
                <?= ($myInfo['role'] == 1) ? "SUPER ADMIN" : "NHÂN VIÊN" ?>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">ID tài khoản:</span>
                    <span>#<?= $myInfo['ma_admin'] ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tên đăng nhập:</span>
                    <span><?= htmlspecialchars($myInfo['ten_dang_nhap']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Quyền hạn:</span>
                    <span style="text-align: right;">
                        <?php 
                            if($myInfo['role'] == 1) echo "Toàn quyền hệ thống";
                            else {
                                $p = explode(',', $myInfo['permissions']);
                                foreach($p as $perm) echo "<span style='background:#333; padding:2px 6px; border-radius:4px; margin-left:5px; font-size:12px;'>$perm</span>";
                            }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </section>
</main>

</body>
</html>