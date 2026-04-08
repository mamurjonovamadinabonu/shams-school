<?php
require_once __DIR__ . '/admin/includes/config.php';
getDB();
$teachersData = fb_request('/teachers') ?: [];
$teachersList = [];
foreach($teachersData as $k => $t) {
    if(!empty($t['active'])) {
        $t['id'] = $k;
        $teachersList[] = $t;
    }
}
$teachersList = array_reverse($teachersList);
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHAMS Xususiy Maktabi — O'qituvchilarimiz</title>
    <meta name="description" content="SHAMS Xususiy Maktabining har bir o'quvchini ilhomlantiradigan 18 nafar fidoyi va tajribali o'qituvchilari bilan tanishing.">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .teachers-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 28px;
        }

        .teacher-card {
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: var(--white);
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid rgba(12, 43, 78, 0.06);
            text-align: center;
        }

        .teacher-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .teacher-photo {
            position: relative;
            height: 180px;
            overflow: hidden;
        }

        .teacher-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            transition: transform 0.5s ease;
        }

        .teacher-card:hover .teacher-photo img {
            transform: scale(1.08);
        }

        .teacher-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 40%, rgba(12, 43, 78, 0.7));
            opacity: 0;
            transition: var(--transition);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 14px;
        }

        .teacher-card:hover .teacher-overlay {
            opacity: 1;
        }

        .teacher-socials {
            display: flex;
            gap: 8px;
        }

        .teacher-social-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: var(--primary);
            transition: var(--transition);
        }

        .teacher-social-btn:hover {
            background: var(--accent);
            color: var(--white);
        }

        .teacher-info {
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
            height: calc(100% - 180px);
        }

        .teacher-name {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 8px;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .teacher-subject {
            color: #E53935;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .teacher-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: auto;
        }

        .teacher-tag {
            background: #ffe9e9;
            color: #E53935;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
        }

        @media (max-width: 1200px) {
            .teachers-grid { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 992px) {
            .teachers-grid { grid-template-columns: repeat(3, 1fr); gap: 20px; }
        }

        @media (max-width: 768px) {
            .teachers-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .teacher-photo { height: 160px; }
            .teacher-name { font-size: 1.05rem; }
            .teacher-subject { font-size: 0.8rem; }
            .teacher-tag { font-size: 0.7rem; padding: 5px 12px; }
        }

        @media (max-width: 500px) {
            .teachers-grid { grid-template-columns: 1fr; gap: 16px; }
            .teacher-photo { height: 200px; }
        }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar" id="navbar">
        <div class="navbar-inner">
            <a href="index.php" class="nav-logo">
                <div class="nav-logo-icon">S</div>
                <div class="nav-logo-text">
                    <span>SHAMS Maktabi</span>
                    <span>Xususiy va Mukammal</span>
                </div>
            </a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="nav-link">Asosiy</a></li>
                <li><a href="groups.php" class="nav-link">Guruhlar</a></li>
                <li><a href="teachers.php" class="nav-link">O'qituvchilar</a></li>
                <li><a href="news.php" class="nav-link">Yangiliklar</a></li>
                <li><a href="contact.php" class="nav-link">Bog'lanish</a></li>
            </ul>
            <button class="hamburger" id="hamburger" aria-label="Menyuni ochish">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- PAGE HERO -->
    <section class="page-hero">
        <div class="container" style="position:relative;z-index:1;">
            <h1>O'qituvchilarimiz bilan Tanishing</h1>
            <p>Kelasi avlod novatorlari, mutafakkirlari va yetakchilarini ilhomlantirishga bag'ishlangan 18 nafar fidoyi pedagog.</p>
            <div class="breadcrumb">
                <a href="index.php">Asosiy</a>
                <span class="sep">/</span>
                <span>O'qituvchilar</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div style="text-align:center;margin-bottom:56px;">
                <h2 class="section-title">Bizning Malakali Pedagoglar</h2>
                <p class="section-subtitle" style="margin:0 auto;">SHAMS'dagi har bir o'qituvchi chuqur bilim, amaliy tajriba va haqiqiy ishtiyoq bilan dars beradi.</p>
            </div>

                        <div class="teachers-grid" id="teachersGrid">
                <?php foreach($teachersList as $teacher): ?>
                <div class="teacher-card reveal">
                    <div class="teacher-photo">
                        <?php if ($teacher['photo']): ?>
                            <img src="<?= htmlspecialchars($teacher['photo']) ?>" alt="<?= htmlspecialchars($teacher['name']) ?>">
                        <?php else: ?>
                            <div style="width:100%;height:100%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:3rem;font-weight:900;color:var(--primary-dark);">
                                <?= mb_substr($teacher['name'],0,1) ?>
                            </div>
                        <?php endif; ?>
                        <div class="teacher-overlay">
                            <div class="teacher-socials">
                                <?php if($teacher['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($teacher['email']) ?>" class="teacher-social-btn">📧</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="teacher-info">
                        <span class="teacher-subject"><?= htmlspecialchars($teacher['subject']) ?></span>
                        <p class="teacher-name"><?= htmlspecialchars($teacher['name']) ?></p>
                        <?php if ($teacher['bio']): ?>
                        <div class="teacher-tags" style="margin-top:10px;">
                            <?php 
                                $tags = explode(',', $teacher['bio']);
                                foreach($tags as $t) {
                                    $t = trim($t);
                                    if(empty($t)) continue;
                                    echo '<span class="teacher-tag">'.htmlspecialchars($t).'</span>';
                                }
                            ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($teacher['experience']): ?>
                        <p class="teacher-exp">⭐ <?= htmlspecialchars($teacher['experience']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="nav-logo">
                        <div class="nav-logo-icon">S</div>
                        <div class="nav-logo-text">
                            <span>SHAMS Maktabi</span>
                            <span>Xususiy va Mukammal</span>
                        </div>
                    </div>
                    <p>2024-yildan buyon yoshlarni yetishtirish, xarakter shakllantirish va global muvaffaqiyatga tayyorlashga bag'ishlangan nufuzli ta'lim muassasasi.</p>
                    <div class="footer-social">
                        <a href="#" class="social-btn" aria-label="Telegram">✈️</a>
                        <a href="#" class="social-btn" aria-label="Instagram">📷</a>
                        <a href="#" class="social-btn" aria-label="YouTube">▶️</a>
                        <a href="#" class="social-btn" aria-label="Facebook">👤</a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Tezkor Havolalar</h4>
                    <ul>
                        <li><a href="index.php">Asosiy</a></li>
                        <li><a href="groups.php">Dasturlarimiz</a></li>
                        <li><a href="teachers.php">O'qituvchilar</a></li>
                        <li><a href="news.php">Maktab Yangiliklari</a></li>
                        <li><a href="contact.php">Qabul</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Yo'nalishlar</h4>
                    <ul>
                        <li><a href="groups.php">Axborot Texnologiyalari</a></li>
                        <li><a href="groups.php">Robototexnika</a></li>
                        <li><a href="groups.php">Aniq Fanlar</a></li>
                        <li><a href="groups.php">Tabiiy Fanlar</a></li>
                        <li><a href="groups.php">Chet Tillari</a></li>
                        <li><a href="groups.php">Xorijda O'qish</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Bog'lanish</h4>
                    <div class="footer-contact-item">
                        <span class="icon">📞</span><span>+998 91 691 6699</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="icon">✈️</span><span>@shamsschool</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="icon">📷</span><span>@shams.school.official</span>
                    </div>
                    <div class="footer-contact-item">
                        <span class="icon">📍</span><span>Farg'ona viloyati, Bag'dod tumani</span>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 SHAMS Xususiy Maktabi. Barcha huquqlar himoyalangan.</p>
                <p>❤️ bilan yaratildi — <a href="index.php">SHAMS Maktabi</a> uchun</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>

</html>
