<?php
require_once __DIR__ . '/admin/includes/config.php';
$db = getDB();
$newsList = $db->query("SELECT * FROM news WHERE published=1 ORDER BY id DESC")->fetchAll();
?>
﻿<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHAMS Xususiy Maktabi — Yangiliklar va Tadbirlar</title>
    <meta name="description" content="SHAMS Xususiy Maktabining so'nggi yangiliklari, tadbirlari va yutuqlari bilan tanishing.">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .news-grid { display: grid; gap: 40px; }

        .news-article {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 0;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--white);
            border: 1px solid rgba(12, 43, 78, 0.06);
            transition: var(--transition);
        }

        .news-article:nth-child(even) { direction: rtl; }
        .news-article:nth-child(even) > * { direction: ltr; }
        .news-article:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); }

        .news-img { position: relative; overflow: hidden; }
        .news-img img { width: 100%; height: 100%; min-height: 300px; object-fit: cover; transition: transform 0.5s ease; }
        .news-article:hover .news-img img { transform: scale(1.06); }

        .news-category {
            position: absolute; top: 20px; left: 20px;
            background: var(--accent); color: var(--white);
            font-size: 0.72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            padding: 6px 14px; border-radius: 20px;
        }

        .news-body {
            padding: 44px 48px;
            display: flex; flex-direction: column; justify-content: center;
        }

        .news-meta { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .news-date { font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
        .news-author { font-size: 0.8rem; color: var(--primary); font-weight: 600; }

        .news-body h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
            color: var(--primary); margin-bottom: 14px; line-height: 1.3;
        }

        .news-body p { font-size: 0.93rem; color: var(--text-muted); line-height: 1.8; margin-bottom: 24px; }

        .news-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
        .news-tag {
            background: var(--light); color: var(--primary);
            font-size: 0.73rem; font-weight: 600;
            padding: 4px 12px; border-radius: 6px;
        }

        .read-more {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--primary); font-size: 0.9rem; font-weight: 700; transition: var(--transition);
        }
        .read-more:hover { color: var(--accent); gap: 14px; }

        .newsletter-block {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            border-radius: var(--radius-lg); padding: 56px 48px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 32px; margin-top: 72px; flex-wrap: wrap;
        }

        .newsletter-text h3 { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--white); margin-bottom: 8px; }
        .newsletter-text p { color: rgba(255,255,255,0.65); font-size: 0.9rem; }

        .newsletter-form { display: flex; gap: 12px; flex-wrap: wrap; }
        .newsletter-form input {
            padding: 13px 20px; border: 2px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08); border-radius: 50px;
            color: var(--white); font-size: 0.9rem; outline: none;
            min-width: 240px; transition: var(--transition);
        }
        .newsletter-form input::placeholder { color: rgba(255,255,255,0.45); }
        .newsletter-form input:focus { border-color: var(--accent); }

        @media (max-width: 900px) {
            .news-article { grid-template-columns: 1fr; }
            .news-article:nth-child(even) { direction: ltr; }
            .news-img img { min-height: 220px; }
            .news-body { padding: 28px; }
            .newsletter-block { flex-direction: column; }
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
            <h1>Maktab Yangiliklari va Tadbirlari</h1>
            <p>SHAMS maktabining jonli hayotini kashf eting — yutuqlarni nishonlang, kelgusi tadbirlarni bilib oling va hamjamiyatimiz bilan bog'liq qoling.</p>
            <div class="breadcrumb">
                <a href="index.php">Asosiy</a>
                <span class="sep">/</span>
                <span>Yangiliklar</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div style="text-align:center;margin-bottom:64px;">
                <h2 class="section-title">So'nggi Yangiliklar</h2>
                <p class="section-subtitle" style="margin:0 auto;">SHAMS maktabi hamjamiyatidan muhim yangilanishlar.</p>
            </div>

            <div class="news-grid">
                <?php foreach($newsList as $news): ?>
                <article class="news-article reveal">
                    <div class="news-img">
                        <?php if ($news['image']): ?>
                            <img src="<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                        <?php else: ?>
                            <div style="width:100%;height:100%;min-height:300px;background:var(--accent);display:flex;align-items:center;justify-content:center;color:var(--primary-dark);font-size:2rem;font-weight:bold;">
                                SHAMS
                            </div>
                        <?php endif; ?>
                        <span class="news-category"><?= htmlspecialchars($news['category']) ?></span>
                    </div>
                    <div class="news-body">
                        <div class="news-meta">
                            <span class="news-date">📅 <?= date('d.m.Y', strtotime($news['created_at'])) ?></span>
                            <span class="news-author">SHAMS Ma'muriyati</span>
                        </div>
                        <h2><?= htmlspecialchars($news['title']) ?></h2>
                        <p><?= nl2br(htmlspecialchars(mb_substr($news['content'], 0, 150))) ?>...</p>
                        <a href="#" class="read-more">To'liq o'qish →</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Yangiliklar obunasi -->
            <div class="newsletter-block reveal">
                <div class="newsletter-text">
                    <h3>📬 Xabardor Bo'lib Turing</h3>
                    <p>SHAMS Yangiliklari obunasiga yozing va tadbirlar, yutuqlar hamda qabul haqida yangilanishlarni o'tkazib yubormang.</p>
                </div>
                <div class="newsletter-form">
                    <input type="email" placeholder="Email manzilingizni kiriting…">
                    <button class="btn btn-primary" type="button">Obuna bo'lish →</button>
                </div>
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
