<h1>Create New Build</h1>

<form action="/builds/store" method="POST"
    style="max-width: 600px; background: var(--darker); padding: 2rem; border-radius: 8px;">

    <div style="margin-bottom: 1.5rem;">
        <label style="display:block; margin-bottom:0.5rem; color:var(--primary);">Character</label>
        <select name="character_id" onchange="window.location.href='/builds/create?character_id='+this.value"
            style="width:100%; padding:0.8rem; background:var(--dark); color:white; border:1px solid #444; border-radius:4px;">
            <option value="">-- Select Character --</option>
            <?php foreach ($characters as $char): ?>
                <option value="<?= $char['id'] ?>" <?= (isset($selectedCharId) && $selectedCharId == $char['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($char['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($selectedCharId): ?>
        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; margin-bottom:0.5rem; color:var(--primary);">Build Name</label>
            <input type="text" name="name" required
                style="width:100%; padding:0.8rem; background:var(--dark); color:white; border:1px solid #444; border-radius:4px;">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; margin-bottom:0.5rem; color:var(--primary);">Description</label>
            <textarea name="description" rows="4"
                style="width:100%; padding:0.8rem; background:var(--dark); color:white; border:1px solid #444; border-radius:4px;"></textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; margin-bottom:0.5rem; color:var(--primary);">Select Skills (Max 3)</label>
            <div style="background:var(--dark); padding:1rem; border-radius:4px; max-height:300px; overflow-y:auto;">
                <?php if (empty($skills)): ?>
                    <p style="color:#aaa;">No skills found for this character.</p>
                <?php else: ?>
                    <?php foreach ($skills as $skill): ?>
                        <div style="margin-bottom:0.5rem;">
                            <label>
                                <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>">
                                <strong><?= htmlspecialchars($skill['name']) ?></strong>
                                <span style="font-size:0.8rem; color:#aaa;">(<?= $skill['cost'] ?>)</span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn" style="width:100%; cursor:pointer;">Save Build</button>
    <?php endif; ?>

</form>