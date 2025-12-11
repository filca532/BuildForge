<h1>Expeditioners of 33</h1>

<div class="grid">
    <?php foreach ($characters as $char): ?>
        <div class="card">
            <?php if (!empty($char['image_url'])): ?>
                <img src="<?= htmlspecialchars($char['image_url']) ?>" alt="<?= htmlspecialchars($char['name']) ?>">
            <?php else: ?>
                <div style="height:200px; background:#333; display:flex; align-items:center; justify-content:center;">No Image
                </div>
            <?php endif; ?>

            <div class="card-body">
                <h3><?= htmlspecialchars($char['name']) ?></h3>
                <p><?= htmlspecialchars(substr($char['description'], 0, 100)) ?>...</p>
                <a href="<?= BASE_URL ?>/character?id=<?= $char['id'] ?>" class="btn"
                    style="width:100%; text-align:center; box-sizing:border-box;">View Skills</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>