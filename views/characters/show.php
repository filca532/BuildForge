<div class="character-header" style="display:flex; gap:2rem; align-items:center; margin-bottom:2rem;">
    <?php if (!empty($character['image_url'])): ?>
        <img src="<?= htmlspecialchars($character['image_url']) ?>" alt="<?= htmlspecialchars($character['name']) ?>"
            style="border-radius:10px; max-width:300px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
    <?php endif; ?>
    <div>
        <h1 style="font-size:3rem; margin-bottom:0.5rem;"><?= htmlspecialchars($character['name']) ?></h1>
        <p style="font-size:1.2rem; opacity:0.8; max-width:800px;">
            <?= nl2br(htmlspecialchars($character['description'])) ?></p>
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