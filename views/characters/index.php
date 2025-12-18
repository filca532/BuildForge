<style>
    .characters-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .characters-title {
        font-family: 'Cinzel', serif;
        font-size: 3rem;
        margin-bottom: 0;
    }

    .character-card {
        position: relative;
        background-image: url('<?= BASE_URL ?>/img/E33/COE33_box_form.webp');
        background-size: 108% 116%;
        background-position: center;
        background-repeat: no-repeat;
        padding: 2rem 2rem 1.5rem;
        min-height: 420px;
        display: flex;
        flex-direction: column;
    }

    .character-card::before {
        content: '';
        position: absolute;
        top: 12px;
        right: 12px;
        width: 70px;
        height: 70px;
        background-image: url('<?= BASE_URL ?>/img/E33/COE33_top_corner_lines.webp');
        background-size: contain;
        background-repeat: no-repeat;
        opacity: 0.4;
        pointer-events: none;
    }

    .character-image {
        width: 100%;
        height: 240px;
        object-fit: cover;
        margin-bottom: 1.2rem;
    }

    .character-name {
        position: relative;
        font-family: 'Cinzel', serif;
        font-size: 1.5rem;
        color: #fff;
        margin: 0 0 0.8rem 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        padding: 0.5rem 1rem;
    }

    .character-name::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200%;
        height: 300%;
        background-image: url('<?= BASE_URL ?>/img/E33/Ultrawide Symetrical Blob.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.12;
        pointer-events: none;
        z-index: -1;
    }

    .character-desc {
        color: #a0a0a0;
        font-size: 0.95rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 1.2rem;
        flex-grow: 1;
    }

    .character-btn {
        display: block;
        width: 100%;
        padding: 0.9rem 1rem;
        background-image: url('<?= BASE_URL ?>/img/E33/COE33_generic_btn.webp');
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        color: #fff;
        font-family: 'Cinzel', serif;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        transition: all 0.3s ease;
        margin-top: auto;
    }

    .character-btn:hover {
        transform: scale(1.03);
        filter: brightness(1.2);
        color: #e6c84a;
    }

    .no-image-placeholder {
        height: 240px;
        background: linear-gradient(135deg, #1a1a1d 0%, #0f0f10 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        font-size: 1rem;
        border: 2px solid rgba(100, 80, 50, 0.3);
        margin-bottom: 1.2rem;
    }
</style>

<div class="characters-header">
    <h1 class="characters-title">Expeditioners of 33</h1>
</div>

<div class="grid">
    <?php foreach ($characters as $char): ?>
        <div class="character-card">
            <?php if (!empty($char['image_url'])): ?>
                <img src="<?= htmlspecialchars($char['image_url']) ?>" alt="<?= htmlspecialchars($char['name']) ?>"
                    class="character-image">
            <?php else: ?>
                <div class="no-image-placeholder">No Image Available</div>
            <?php endif; ?>

            <h3 class="character-name"><?= htmlspecialchars($char['name']) ?></h3>
            <p class="character-desc"><?= htmlspecialchars($char['description']) ?></p>
            <a href="<?= BASE_URL ?>/character?id=<?= $char['id'] ?>" class="character-btn">View Skills</a>
        </div>
    <?php endforeach; ?>
</div>