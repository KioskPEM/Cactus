class Bubble {

    constructor(x, y, radius, type, texture, score) {
        this.x = x;
        this.y = y;

        this.xVel = (Math.random() * 200) - 100;
        this.yVel = -(30 + Math.random() * 15);

        this.radius = radius || ((Math.random() <= .01) ? this.maxSize : 20 + (Math.random() * 10));

        this.type = type || 'normal';

        if (texture) {
            this.texture = texture;
            this.color = undefined;
        } else {
            let defaultTextures = [
                'blue',
                'purple',
                'red',
                'yellow'
            ];

            let bubbleColor = defaultTextures[Math.floor(Math.random() * defaultTextures.length)];
            this.texture = Bubulator.textures[bubbleColor];
            this.color = bubbleColor;
        }

        this.score = score !== undefined ? score : Math.floor(this.radius * 4);

        this.y += this.radius;

        this.invulnerabilityDuration = .1;
        this.gravity = true;
        this.deathTimer = -1;
    }

    update(deltaTime, width, height) {
        // handle gravity
        if (this.gravity) {
            this.yVel += (this.acceleration * deltaTime);
            if (this.yVel < this.maxSpeed)
                this.yVel = this.maxSpeed;

            this.xVel *= 1 / (1 + (deltaTime * this.friction));
        }

        // update position
        this.y = this.y + (this.yVel * deltaTime);
        this.x = this.x + (this.xVel * deltaTime);

        if (this.x < -this.radius || this.x > this + this.radius || this.y < -this.radius || this.y > height + this.radius) {
            this.onBecameInvisible();
        }

        if (this.invulnerabilityDuration > 0) {
            this.invulnerabilityDuration -= deltaTime;
            if (this.invulnerabilityDuration < 0)
                this.invulnerabilityDuration = 0;
        }
    }

    draw(ctx) {
        let size = this.radius * 2;
        ctx.drawImage(this.texture, this.x - this.radius, this.y - this.radius, size, size);
    }

    burst() {
        if (this.invulnerable)
            return;

        this.texture = Bubulator.textures.burst;

        this.xVel *= .5;
        this.yVel *= .5;
        this.deathTimer = .25;
        this.invulnerabilityDuration = -1;
    }

    onBecameInvisible() {
        this.deathTimer = 0;
    }

    collidePoint(x, y) {
        let dx = this.x - x;
        let dy = this.y - y;
        return dx * dx + dy * dy <= this.radius * this.radius;
    }

    distance(bubble) {
        let dx = this.x - bubble.x;
        let dy = this.y - bubble.y;
        return Math.sqrt(dx * dx + dy * dy);
    }

    get invulnerable() {
        return this.invulnerabilityDuration !== 0;
    }

}

class BubbleSpecial extends Bubble {

    constructor(x, y) {
        super(x, y, undefined, 'special', Bubulator.textures.nyuu, 0);
    }

    burst() {
        if (this.invulnerable)
            return;

        super.burst();

        Bubulator.spawnBubble(new FallingBubble(
            this.x,
            this.y,
            this.radius * .8,
            'burst',
            Bubulator.textures.nyuu_burst,
            Math.floor(200 + (this.maxSize - this.radius) * 5),
            (Math.random() <= .15) ? -125 + (-8 * this.radius) : 350
        ));
    }

}

class FallingBubble extends Bubble {

    constructor(x, y, radius, type, texture, score, yVel) {
        super(x, y, radius, type, texture, score);

        this.yVel = yVel;
        this.gravity = false;
    }

    update(deltaTime, width, height) {
        this.yVel += (25 * this.radius) * deltaTime;
        super.update(deltaTime, width, height);
    }

    onBecameInvisible() {
        super.onBecameInvisible();
        if (Bubulator.hasScore()) {
            let score = Bubulator.score;
            score.addScore(-this.score * 2);
        }
    }

}

class BlackHole extends Bubble {

    constructor(x, y) {
        super(x, y, 15, 'black_hole', Bubulator.textures.black, 0);
    }

    burst() {
        if (this.invulnerable)
            return;

        super.burst();

        for (let bubble of Bubulator.bubbles) {
            if (bubble.type === 'normal') {
                if (Bubulator.hasScore()) {
                    Bubulator.score.bonusMultiplier = 1.5;
                    Bubulator.score.penaltyMultiplier = 0;
                    Bubulator.score.addScore(bubble.score);
                }

                bubble.burst();
            }
        }
        if (Bubulator.hasScore()) {
            Bubulator.score.bonusMultiplier = 1;
            Bubulator.score.penaltyMultiplier = 1;

            Bubulator.score.bonusAnimation = 'smaller';
            Bubulator.score.penaltyAnimation = 'smaller';
        }
    }

}

class Bomb extends Bubble {

    constructor(x, y) {
        super(x, y, 15, 'bomb', Bubulator.textures.black, -200);
    }

    burst() {
        if (this.invulnerable)
            return;

        this.texture = Bubulator.textures.red_burst;

        this.xVel *= .2;
        this.yVel *= .2;
        this.deathTimer = .25;
        this.invulnerabilityDuration = -1;

        this.radius = 200;

        for (let bubble of Bubulator.bubbles) {
            let distance = this.distance(bubble);

            if (distance <= this.radius + bubble.radius) {
                if (bubble.type === 'special' || bubble.type === 'bomb')
                    bubble.burst();
                else if (bubble.type !== 'burst') {
                    let dX = (bubble.x - this.x);
                    let dY = (bubble.y - this.y);

                    let radius = this.radius + bubble.radius;
                    let force = (radius - distance) / radius * 8;

                    bubble.xVel += dX * force;
                    bubble.yVel += dY * force;

                    if (Bubulator.hasScore()) {
                        Bubulator.score.bonusMultiplier = 1;
                        Bubulator.score.penaltyMultiplier = 1;
                        Bubulator.score.addScore(-bubble.score * 2);
                    }
                }
            }
        }
    }

}

Bubble.prototype.maxSpeed = -75;
Bubble.prototype.friction = 1;
Bubble.prototype.acceleration = -125;
Bubble.prototype.maxSize = 150;