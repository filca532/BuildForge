<h1><?= htmlspecialchars($title) ?></h1>

<div
    style="background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #333; font-family: 'Consolas', monospace; max-height: 600px; overflow-y: auto; margin-bottom: 2rem;">
    <?php foreach ($logs as $log): ?>
        <div style="margin-bottom: 4px; border-bottom: 1px solid #2a2a2a; padding-bottom: 2px;">
            <?php if (stripos($log, 'Error') !== false || stripos($log, 'Warning') !== false): ?>
                <span style="color: #ff6b6b;"><?= htmlspecialchars($log) ?></span>
            <?php elseif (stripos($log, 'Saved') !== false): ?>
                <span style="color: #4cd137;"><?= htmlspecialchars($log) ?></span>
            <?php elseif (stripos($log, 'Found') !== false): ?>
                <span style="color: #fbc531;"><?= htmlspecialchars($log) ?></span>
            <?php else: ?>
                <span style="color: #bdc3c7;"><?= htmlspecialchars($log) ?></span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<a href="<?= BASE_URL ?>" class="btn">Return to Home</a>