<div class="character-header" style="
    display:flex; 
    gap:4rem; 
    align-items:center; 
    margin-bottom:2rem; 
    padding: 3rem; 
    background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.7)), url('<?= BASE_URL ?>/img/gold_leaf_bg.jpg'); 
    background-size: cover; 
    background-position: center;
    border-radius: 12px;
    box-shadow: 0 4px 30px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,215,0, 0.2);
">
    <?php if (!empty($character['image_url'])): ?>
        <?php
        $imgSrc = (strpos($character['image_url'], 'http') === 0)
            ? $character['image_url']
            : BASE_URL . '/' . $character['image_url'];
        ?>
        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($character['name']) ?>" style="
                border-radius:50%; 
                width: 250px; 
                height: 250px; 
                object-fit:cover; 
                box-shadow: 0 0 0 5px rgba(255, 215, 0, 0.3);
                border: 2px solid var(--primary);
            ">
    <?php endif; ?>
    <div style="flex:1;">
        <h1
            style="font-size:3.5rem; margin-bottom:0.5rem; font-family: 'Cinzel', serif; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); color: var(--primary);">
            <?= htmlspecialchars($character['name']) ?></h1>
        <p style="font-size:1.3rem; line-height:1.6; color: #e0e0e0; max-width:900px; text-shadow: 1px 1px 2px black;">
            <?= nl2br(htmlspecialchars($character['description'])) ?>
        </p>
    </div>
</div>

<h2>Skills</h2>
<?php if (empty($skills)): ?>
    <div style="padding:2rem; background:rgba(255,0,0,0.1); border-left:4px solid red;">
        No skills found for this character. (Migration issue?)
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($skills as $skill): ?>
            <div class="card" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h3 style="margin:0; font-size:1.2rem;"><?= htmlspecialchars($skill['name']) ?></h3>
                        <span
                            style="background:var(--primary); color:var(--darker); padding:2px 8px; border-radius:4px; font-size:0.8rem; font-weight:bold;">
                            <?= htmlspecialchars($skill['cost']) ?>
                        </span>
                    </div>
                    <div style="margin:0.5rem 0; font-size:0.9rem; color:#aaa;">Type: <?= htmlspecialchars($skill['type']) ?>
                    </div>
                    <p><?= htmlspecialchars($skill['description']) ?></p>
                    <?php if ($skill['damage'] > 0): ?>
                        <div style="margin-top:0.5rem; color:#ff6b6b; font-weight:bold;">Damage: <?= $skill['damage'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="margin-top: 3rem;">
    <a href="/characters" class="btn">&larr; Back to Characters</a>
</div>