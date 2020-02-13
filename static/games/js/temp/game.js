const Bubulator = function () {

    const BUBBLE_SPAWN_DELAY = .5;
    const MAX_BUBBLE = 50;

    let canvas = document.getElementById('game-canvas');
    let context = canvas.getContext('2d');

    let running = true;

    let lastFrameTime = 0;

    let textures = {
        blue: undefined,
        purple: undefined,
        red: undefined,
        yellow: undefined,
        black: undefined,
        nyuu: undefined,
        burst: undefined,
        red_burst: undefined,
        nyuu_burst: undefined
    };
    let loadedTextures = 0;

    let bubbles = [];
    let score;

    let lastBubbleColor = null;

    let timeBeforeBubbleSpawn = 0;

    function init() {
        window.addEventListener('resize', function () {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            draw();
        });

        document.body.addEventListener('click', handleMouseClick);

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        if (typeof Score === 'function')
            score = new Score();

        let textureCount = 0;
        for (let name in textures) {
            textureCount++;

            let image = new Image();
            image.src = 'https://assets.lesnotesdeyuma.fr/img/bubble/' + name + '.png';
            image.onload = function () {
                loadedTextures++;
                console.log('Loaded', name, `(${loadedTextures} / ${textureCount})`);
                if (loadedTextures === textureCount)
                    window.requestAnimationFrame(update);
            };

            textures[name] = image;
        }
    }

    function update(time) {
        let deltaTime = (time - lastFrameTime) / 1000;

        timeBeforeBubbleSpawn -= deltaTime;
        if (running && bubbles.length < MAX_BUBBLE && timeBeforeBubbleSpawn <= 0) {
            timeBeforeBubbleSpawn = BUBBLE_SPAWN_DELAY;
            spawnRandomBubble(
                Math.random() * canvas.width,
                canvas.height
            );
        }

        if (score)
            score.update(deltaTime);

        for (let bubble of bubbles) {
            bubble.update(deltaTime, canvas.width, canvas.height);

            if (bubble.deathTimer >= 0) {
                bubble.deathTimer -= deltaTime;
                if (bubble.deathTimer <= 0)
                    deleteBubble(bubble);
            }
        }

        draw();

        lastFrameTime = time;

        if (running || bubbles.length > 0)
            window.requestAnimationFrame(update);
    }

    function draw() {
        context.clearRect(0, 0, canvas.width, canvas.height);

        if (score)
            score.draw(context, canvas.width, canvas.height);

        for (let bubble of bubbles)
            bubble.draw(context);

        context.fillStyle = 'black';

        let gradient = context.createLinearGradient(0, canvas.height - 150, 0, canvas.height);
        gradient.addColorStop(0, 'transparent');
        gradient.addColorStop(1, 'black');
        context.fillStyle = gradient;
        context.fillRect(0, canvas.height - 150, canvas.width, canvas.height);
    }

    function spawnRandomBubble(x, y) {
        if (Math.random() <= .1)
            spawnBubble(new BubbleSpecial(x, y));
        else if (Math.random() < .02)
            spawnBubble(new Bomb(x, y));
        else if (Math.random() < .01)
            spawnBubble(new BlackHole(x, y));
        else
            spawnBubble(new Bubble(x, y));
    }

    function spawnBubble(bubble) {
        bubbles.push(bubble);
    }

    function deleteBubble(bubble) {
        let index = bubbles.indexOf(bubble);
        if (index > -1)
            bubbles.splice(index, 1);
    }

    function handleMouseClick(e) {
        let mX = e.clientX;
        let mY = e.clientY;

        for (let bubble of bubbles) {
            if (!bubble.invulnerable && bubble.collidePoint(mX, mY)) {
                bubble.burst();

                if (score && bubble.type === 'normal') {
                    if (bubble.color === lastBubbleColor) {
                        let bonusMod = Math.min(score.bonusMultiplier + .3, score.maxBonusModifier);
                        if (score.bonusMultiplier !== bonusMod) {
                            score.bonusMultiplier = bonusMod;
                            score.bonusAnimation = 'bigger';
                        }

                        let penaltyMod = Math.max(bonusMod * .75, 1);
                        if (score.penaltyMultiplier !== penaltyMod) {
                            score.penaltyMultiplier = penaltyMod;
                            score.penaltyAnimation = 'bigger';
                        }
                    } else {
                        lastBubbleColor = bubble.color;
                        if (score.bonusMultiplier !== 1) {
                            score.bonusMultiplier = 1;
                            score.bonusAnimation = 'smaller';
                        }

                        if (score.penaltyMultiplier !== 1) {
                            score.penaltyMultiplier = 1;
                            score.penaltyAnimation = 'smaller';
                        }
                    }
                }

                if (score)
                    score.addScore(bubble.score);
            }
        }
    }

    init();

    return {
        spawnBubble,
        bubbles,
        score,
        textures,
        hasScore: function () {
            return score !== undefined;
        },
        isRunning: function () {
            return running;
        },
        stop: function () {
            running = false;
            Cookies.set('no-bubble', true, {expires: 365});
        },
        start: function () {
            if (!running) {
                running = true;
                Cookies.remove('no-bubble');
                window.requestAnimationFrame(update);
            }
        }
    }

}();