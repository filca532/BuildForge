<style>
    .character-header {
        position: relative;
        display: flex;
        gap: 3rem;
        align-items: center;
        margin-bottom: 2rem;
        padding: 3rem 4rem;
        background-image: url('<?= BASE_URL ?>/img/E33/gold_leaf_bg.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 12px;
        border: 2px solid rgba(214, 138, 40, 0.4);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
    }

    .character-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 50%, rgba(0, 0, 0, 0.7) 100%);
        border-radius: 12px;
        pointer-events: none;
    }

    .character-avatar {
        border-radius: 50%;
        width: 180px;
        height: 180px;
        object-fit: cover;
        box-shadow: 0 0 0 4px rgba(214, 138, 40, 0.7);
        border: 3px solid #000;
        z-index: 2;
        position: relative;
    }

    .character-info {
        flex: 1;
        z-index: 2;
        position: relative;
    }

    .character-info h1 {
        font-size: 3rem;
        margin-bottom: 0.8rem;
        font-family: 'Cinzel', serif;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 1);
        color: #fff;
        background: none;
        -webkit-text-fill-color: #fff;
    }

    .character-info h1::after {
        display: none;
    }

    .character-info p {
        font-size: 1.15rem;
        line-height: 1.7;
        color: #e0e0e0;
        text-shadow: 1px 1px 3px black;
    }
</style>

<div class="character-header">
    <?php if (!empty($character['image_url'])): ?>
        <?php
        $imgSrc = (strpos($character['image_url'], 'http') === 0)
            ? $character['image_url']
            : BASE_URL . '/' . $character['image_url'];
        ?>
        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($character['name']) ?>"
            class="character-avatar">
    <?php endif; ?>
    <div class="character-info">
        <h1><?= htmlspecialchars($character['name']) ?></h1>
        <p><?= nl2br(htmlspecialchars($character['description'])) ?></p>
    </div>
</div>

<div style="text-align: center; margin-bottom: 2rem;">
    <img src="<?= BASE_URL ?>/img/E33/Line.webp" alt="Divider" style="width: 80%; height: auto; opacity: 0.8;">
</div>

<!-- Tab Navigation -->
<style>
    .tab-nav {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .tab-btn {
        display: inline-block;
        padding: 1rem 3rem;
        font-family: 'Cinzel', serif;
        font-size: 1.2rem;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        color: #888;
        border: 2px solid rgba(100, 80, 50, 0.3);
        border-radius: 4px;
    }

    .tab-btn:hover {
        color: #d68a28;
        border-color: rgba(214, 138, 40, 0.5);
    }

    .tab-btn.active {
        background-image: url('<?= BASE_URL ?>/img/E33/COE33_generic_btn.webp');
        color: #fff;
        border-color: transparent;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    .tab-btn.active:hover {
        filter: brightness(1.1);
    }
</style>

<div class="tab-nav">
    <a href="<?= BASE_URL ?>/character?id=<?= $character['id'] ?>&tab=skills"
        class="tab-btn <?= $activeTab === 'skills' ? 'active' : '' ?>">
        Skills
    </a>
    <a href="<?= BASE_URL ?>/character?id=<?= $character['id'] ?>&tab=weapons"
        class="tab-btn <?= $activeTab === 'weapons' ? 'active' : '' ?>">
        Weapons
    </a>
</div>

<?php if ($activeTab === 'skills'): ?>
    <!-- Skills Section -->
    <h2
        style="font-family: 'Cinzel', serif; color: #d68a28; text-align: center; margin-bottom: 2rem; font-size: 2.5rem; text-shadow: 0 0 10px rgba(0,0,0,0.8);">
        Skills</h2>

    <?php if (empty($skills)): ?>
        <div style="padding:2rem; background:rgba(255,0,0,0.1); border-left:4px solid red; margin: 0 auto; max-width: 800px;">
            No skills found for this character. (Migration issue?)
        </div>
    <?php else: ?>
        <style>
            .skill-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
                gap: 2rem;
                padding: 1rem;
            }

            .skill-card {
                position: relative;
                background-image: url('<?= BASE_URL ?>/img/E33/BackgroundTILE.webp');
                background-size: cover;
                background-position: center;
                background-repeat: repeat;
                background-color: #0a0a0b;
                border: 2px solid rgba(214, 138, 40, 0.5);
                border-radius: 12px;
                padding: 2rem 2.5rem;
                display: flex;
                gap: 1.5rem;
                align-items: flex-start;
                min-height: 180px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .skill-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0.4) 100%);
                border-radius: 12px;
                pointer-events: none;
            }

            .skill-card:hover {
                transform: translateY(-5px);
                border-color: rgba(214, 138, 40, 0.8);
                box-shadow: 0 12px 30px rgba(214, 138, 40, 0.15);
            }

            .skill-icon-wrapper {
                flex-shrink: 0;
                padding-top: 5px;
                position: relative;
                z-index: 1;
            }

            .skill-icon-diamond {
                width: 80px;
                height: 80px;
                transform: rotate(45deg);
                overflow: hidden;
                border: 3px solid #6d5e45;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
                margin: 15px;
                background: #000;
                transition: border-color 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .skill-card:hover .skill-icon-diamond {
                border-color: #d68a28;
                box-shadow: 0 0 15px rgba(214, 138, 40, 0.5);
            }

            .skill-icon-diamond img {
                width: 160%;
                height: 160%;
                transform: rotate(-45deg);
                object-fit: cover;
                flex-shrink: 0;
            }

            .skill-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                z-index: 1;
                /* Above corner lines */
            }

            .skill-header {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                margin-bottom: 12px;
                border-bottom: 1px solid #333;
                /* Could replace with Line.webp but might be too thick */
                padding-bottom: 8px;
                transition: border-color 0.3s ease;
            }

            .skill-card:hover .skill-header {
                border-bottom-color: rgba(214, 138, 40, 0.3);
            }

            .skill-title {
                font-family: 'Cinzel', serif;
                font-size: 1.6rem;
                color: #efefef;
                margin: 0;
                text-shadow: 1px 1px 2px #000;
            }

            .skill-cost {
                font-family: 'Cinzel', serif;
                font-size: 1.8rem;
                color: #d68a28;
                font-weight: bold;
                text-transform: uppercase;
                padding-right: 1rem;
                /* Space from corner lines */
            }

            .skill-body {
                font-size: 1rem;
                line-height: 1.7;
                color: #ccc;
                font-family: 'Roboto', sans-serif;
            }

            .skill-body img {
                height: 1.5em;
                width: auto;
                min-width: 1.5em;
                object-fit: contain;
                vertical-align: text-bottom;
                margin: 0 4px;
            }

            .damage-tag {
                display: inline-block;
                margin-top: 12px;
                color: #ff6b6b;
                font-weight: bold;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                background: rgba(255, 107, 107, 0.1);
                padding: 2px 6px;
                border-radius: 4px;
            }
        </style>

        <div class="skill-grid">
            <?php foreach ($skills as $skill): ?>
                <div class="skill-card">
                    <div class="skill-icon-wrapper">
                        <div class="skill-icon-diamond">
                            <?php if (!empty($skill['icon_path'])): ?>
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($skill['icon_path']) ?>" alt="Icon">
                            <?php else: ?>
                                <div style="background:#222; width:100%; height:100%;"></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="skill-content">
                        <div class="skill-header">
                            <h3 class="skill-title"><?= htmlspecialchars($skill['name']) ?></h3>
                            <div class="skill-cost"><?= htmlspecialchars($skill['cost']) ?> AP</div>
                        </div>

                        <div class="skill-body">
                            <?= strip_tags($skill['description'], '<img><br><b><strong><i><em><p>') ?>
                            <?php if ($skill['damage'] > 0): ?>
                                <br><span class="damage-tag">Damage: <?= $skill['damage'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($activeTab === 'weapons'): ?>
    <!-- Weapons Section -->
    <h2
        style="font-family: 'Cinzel', serif; color: #d68a28; text-align: center; margin-bottom: 2rem; font-size: 2.5rem; text-shadow: 0 0 10px rgba(0,0,0,0.8);">
        Weapons</h2>

    <?php if (empty($weapons)): ?>
        <div
            style="padding:2rem; background:rgba(100,80,50,0.1); border-left:4px solid #d68a28; margin: 0 auto; max-width: 800px; text-align: center;">
            No weapons found for this character. Run a migration to import weapons data.
        </div>
    <?php else: ?>
        <style>
            .weapon-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 2rem;
                padding: 1rem;
            }

            .weapon-card {
                position: relative;
                background-image: url('<?= BASE_URL ?>/img/E33/BackgroundTILE.webp');
                background-size: cover;
                background-position: center;
                border-radius: 12px;
                border: 2px solid rgba(214, 138, 40, 0.5);
                padding: 1.5rem;
                text-align: center;
                transition: all 0.3s ease;
            }

            .weapon-card:hover {
                transform: translateY(-5px);
                border-color: rgba(214, 138, 40, 0.8);
                box-shadow: 0 10px 30px rgba(214, 138, 40, 0.2);
            }

            .weapon-image {
                width: 120px;
                height: 120px;
                object-fit: contain;
                margin: 0 auto 1rem;
                display: block;
                filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.5));
            }

            .weapon-name {
                font-family: 'Cinzel', serif;
                font-size: 1.2rem;
                color: #fff;
                margin: 0 0 0.5rem 0;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            }

            <div class="weapon-grid">
            <?php foreach ($weapons as $weapon): ?>
                <div class="weapon-card">
                <?php if (!empty($weapon['image_url'])): ?>
                    <img src="<?= htmlspecialchars($weapon['image_url']) ?>" alt="<?= htmlspecialchars($weapon['name']) ?>"
                    class="weapon-image">
                <?php else: ?>
                    <div class="no-weapon-image">No Image</div>
                <?php endif; ?>

                <h3 class="weapon-name"><?= htmlspecialchars($weapon['name']) ?></h3></div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 4rem; padding-bottom: 2rem;"><a href="<?= BASE_URL ?>/characters" class="back-btn">Back to Characters </a><style>.back-btn {
        display: inline-block;
        padding: 1rem 3rem;
        background-image: url('<?= BASE_URL ?>/img/E33/COE33_generic_btn.webp');
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        color: #fff;
        font-family: 'Cinzel', serif;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        transform: scale(1.05);
        filter: brightness(1.2);
        color: #e6c84a;
    }
</style>
</div>