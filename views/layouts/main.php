<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BuildForge' ?> | Expedition 33</title>
    <style>
        :root {
            --primary: #d4a373;
            --secondary: #faedcd;
            --dark: #1a1a1d;
            --darker: #0f0f10;
            --text: #e1e1e1;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--dark);
            color: var(--text);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        header {
            background-color: var(--darker);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
        }

        nav a {
            color: var(--text);
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: var(--primary);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h1,
        h2,
        h3 {
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: var(--darker);
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: opacity 0.3s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Grid for cards */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--darker);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1rem;
        }

        footer {
            text-align: center;
            padding: 2rem;
            background: var(--darker);
            margin-top: 3rem;
            font-size: 0.9rem;
            opacity: 0.7;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <h2 style="margin:0;">BuildForge <span style="font-size:0.8em; color:var(--secondary)">Obsidian</span></h2>
        </div>
        <nav>
            <a href="<?= BASE_URL ?>/">Home</a>
            <a href="<?= BASE_URL ?>/characters">Characters</a>
            <a href="<?= BASE_URL ?>/builds">My Builds</a>
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