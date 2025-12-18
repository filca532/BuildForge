<style>
    .hero {
        position: relative;
        text-align: center;
        padding: 6rem 2rem;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        max-width: 900px;
        height: 400px;
        background-image: url('<?= BASE_URL ?>/img/E33/Ultrawide Symetrical Blob.png');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.15;
        pointer-events: none;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-family: 'Cinzel', serif;
        font-size: 4rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #e6c84a 0%, #d68a28 40%, #c9a227 70%, #e9a84a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: none;
        letter-spacing: 3px;
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {

        0%,
        100% {
            filter: brightness(1);
        }

        50% {
            filter: brightness(1.2);
        }
    }

    .hero-subtitle {
        font-size: 1.3rem;
        max-width: 700px;
        margin: 0 auto 3rem;
        color: #c0c0c0;
        line-height: 1.8;
    }

    .hero-subtitle strong {
        color: #d68a28;
        font-weight: 600;
    }

    .hero-divider {
        width: 300px;
        height: 20px;
        margin: 0 auto 3rem;
        background-image: url('<?= BASE_URL ?>/img/E33/Line.webp');
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.7;
    }

    /* Game Selection Section */
    .game-selector {
        margin-bottom: 3rem;
    }

    .game-selector-title {
        font-family: 'Cinzel', serif;
        font-size: 1.2rem;
        color: #a0a0a0;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .game-cards {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .game-card {
        position: relative;
        background: rgba(15, 15, 16, 0.9);
        border: 2px solid rgba(214, 138, 40, 0.3);
        border-radius: 12px;
        padding: 1.5rem 2.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        min-width: 200px;
    }

    .game-card:hover,
    .game-card.active {
        border-color: rgba(214, 138, 40, 0.8);
        background: rgba(214, 138, 40, 0.1);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(214, 138, 40, 0.15);
    }

    .game-card.active {
        border-color: #d68a28;
    }

    .game-card-name {
        font-family: 'Cinzel', serif;
        font-size: 1.1rem;
        color: #fff;
        margin: 0;
    }

    .game-card-desc {
        font-size: 0.85rem;
        color: #888;
        margin-top: 0.5rem;
    }

    .hero-buttons {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .hero-btn {
        display: inline-block;
        min-width: 220px;
        padding: 1.2rem 2.5rem;
        font-family: 'Cinzel', serif;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .hero-btn-primary {
        background-image: url('<?= BASE_URL ?>/img/E33/COE33_generic_btn.webp');
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        color: #fff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }

    .hero-btn-primary:hover {
        transform: scale(1.08);
        filter: brightness(1.3);
        color: #e6c84a;
    }

    .hero-btn-secondary {
        background-image: url('<?= BASE_URL ?>/img/E33/ButtonBG.webp');
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        color: #d68a28;
        border: none;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .hero-btn-secondary:hover {
        transform: scale(1.08);
        filter: brightness(1.2);
        color: #e9a84a;
    }

    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 4rem;
        padding: 2rem;
    }

    .feature-card {
        text-align: center;
        padding: 2rem;
        background: rgba(15, 15, 16, 0.8);
        border: 1px solid rgba(214, 138, 40, 0.2);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        border-color: rgba(214, 138, 40, 0.5);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .feature-title {
        font-family: 'Cinzel', serif;
        font-size: 1.3rem;
        color: #d68a28;
        margin-bottom: 0.8rem;
    }

    .feature-desc {
        color: #a0a0a0;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }

        .game-cards {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<div class="hero">
    <div class="hero-content">
        <h1 class="hero-title">BuildForge</h1>
        <div class="hero-divider"></div>

        <!-- Game Selection -->
        <div class="game-selector">
            <p class="game-selector-title">Select Your Game</p>
            <div class="game-cards">
                <?php foreach ($games as $index => $game): ?>
                    <div class="game-card<?= $index === 0 ? ' active' : '' ?>" data-game-id="<?= $game['id'] ?>">
                        <h3 class="game-card-name"><?= htmlspecialchars($game['name']) ?></h3>
                        <?php if (!empty($game['description'])): ?>
                            <p class="game-card-desc"><?= htmlspecialchars(substr($game['description'], 0, 60)) ?>...</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <p class="hero-subtitle">
            The ultimate theory-crafting tool for your favorite RPGs.
            Create, share, and discover the most powerful builds.
        </p>
        <div class="hero-buttons">
            <a href="<?= BASE_URL ?>/characters" class="hero-btn hero-btn-primary">Explore Characters</a>
            <a href="<?= BASE_URL ?>/builds/create" class="hero-btn hero-btn-secondary">Create Build</a>
        </div>
    </div>
</div>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon">⚔️</div>
        <h3 class="feature-title">Character Skills</h3>
        <p class="feature-desc">Browse detailed skill trees and abilities for all playable characters.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">📋</div>
        <h3 class="feature-title">Build Creator</h3>
        <p class="feature-desc">Craft and save your custom builds with optimal skill combinations.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🌟</div>
        <h3 class="feature-title">Community Builds</h3>
        <p class="feature-desc">Discover top-rated builds shared by the gaming community.</p>
    </div>
</div>