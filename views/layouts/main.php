<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BuildForge' ?> | Expedition 33</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Outfit:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">

    <!-- Dynamic styles that require PHP -->
    <style>
        header {
            background-image: url('<?= BASE_URL ?>/img/E33/Header Frame-min.webp');
        }

        .btn {
            background-image: url('<?= BASE_URL ?>/img/E33/COE33_generic_btn.webp');
        }

        h1::after {
            background-image: url('<?= BASE_URL ?>/img/E33/Lead the members Line.webp');
        }

        .card {
            background-image: url('<?= BASE_URL ?>/img/E33/COE33_box_form.webp');
        }

        .card::before {
            background-image: url('<?= BASE_URL ?>/img/E33/COE33_top_corner_lines.webp');
        }

        footer::before {
            background-image: url('<?= BASE_URL ?>/img/E33/Lead the members Line.webp');
        }

        .btn-danger {
            background-image: url('<?= BASE_URL ?>/img/E33/COE33_generic_btn_red.webp');
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <h2>BuildForge <span>Obsidian</span></h2>
        </div>
        <nav>
            <a href="<?= BASE_URL ?>/">Home</a>
            <a href="<?= BASE_URL ?>/characters">Characters</a>
            <a href="<?= BASE_URL ?>/builds">My Builds</a>
            <a href="<?= BASE_URL ?>/admin/migrate" class="btn btn-danger">Reset Data</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/logout" class="btn">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="btn">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <?= $content ?? '' ?>
    </main>

    <footer>
        &copy; <?= date('Y') ?> BuildForge - Expedition 33 Fan Project
    </footer>

</body>

</html>