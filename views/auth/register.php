<h1>Register</h1>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div
        style="background:rgba(255,0,0,0.2); border:1px solid red; padding:1rem; margin-bottom:1rem; color:red; border-radius:4px;">
        <?= $_SESSION['flash_error'];
        unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<form action="/register/store" method="POST"
    style="max-width: 400px; background: var(--darker); padding: 2rem; border-radius: 8px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <label style="display:block; margin-bottom:0.5rem; color:var(--primary);">Username</label>
        <input type="text" name="username" required
            style="width:100%; padding:0.8rem; background:var(--dark); color:white; border:1px solid #444; border-radius:4px;">
    </div>

    <div style="margin-bottom: 1.5rem;">
        <label style="display:block; margin-bottom:0.5rem; color:var(--primary);">Password</label>
        <input type="password" name="password" required
            style="width:100%; padding:0.8rem; background:var(--dark); color:white; border:1px solid #444; border-radius:4px;">
    </div>

    <button type="submit" class="btn" style="width:100%; cursor:pointer;">Register</button>
</form>