<h1>Community Builds</h1>

<div style="margin-bottom: 2rem;">
    <a href="/builds/create" class="btn">Create New Build</a>
</div>

<?php if (empty($builds)): ?>
    <p>No builds created yet. Be the first!</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($builds as $build): ?>
            <div class="card">
                <div class="card-body">
                    <h3><?= htmlspecialchars($build['name']) ?></h3>
                    <div style="font-size:0.9rem; color:#aaa; margin-bottom:1rem;">
                        by <span style="color:var(--primary)"><?= htmlspecialchars($build['username']) ?></span> 
                        for <span style="color:var(--secondary)"><?= htmlspecialchars($build['character_name']) ?></span>
                    </div>
                    <p><?= htmlspecialchars(substr($build['description'], 0, 100)) ?>...</p>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $build['user_id']): ?>
                        <form action="/builds/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            <input type="hidden" name="id" value="<?= $build['id'] ?>">
                            <button type="submit" class="btn" style="background:#ff6b6b; padding:0.3rem 0.6rem; margin-top:1rem; font-size:0.8rem; border:none; cursor:pointer;">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>