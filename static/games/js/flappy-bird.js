class Cat extends SpriteSheetObject {

    constructor(x, y, texture) {
        super("cat", x, y, texture, 3, 4, 0.07);

        this.jumped = false;
        this.gravity = 1000;
        this.velY = 0;
    }

    update(game, deltaTime) {
        super.update(game, deltaTime);

        this.velY += this.gravity * deltaTime;
        if (this.jumped) {
            this.jumped = false;
            this.velY = -300;
        }
        this.y += this.velY * deltaTime;

        let halfHeight = this.height / 2;
        if (this.y < -halfHeight || this.y > game.canvas.height - halfHeight) {
            this.kill();
            return;
        }

        for (let object of game.objects) {
            if (object instanceof Pipe && object.intersect(this)) {
                //game.context.fillRect(this.x, this.y, this.width, this.height);
                this.kill();
                return;
            }
        }
    }

    kill() {
        this.remove();
        window.location.href = DEATH_PAGE;
    }

}

class PipeSpawner extends GameObject {

    /**
     *
     * @param {number} spawnDelay
     * @param {string} pipeTexture
     * @param {number} pipeSpeed
     */
    constructor(spawnDelay, pipeTexture, pipeSpeed) {
        super("pipe_spawner", 0, 0);

        this.spawnDelay = spawnDelay;
        this.pipeTexture = pipeTexture;
        this.pipeSpeed = pipeSpeed
        this.time = spawnDelay;
    }

    update(game, deltaTime) {
        super.update(game, deltaTime);

        this.time += deltaTime;
        if (this.time >= this.spawnDelay) {
            this.time = 0;

            let x = game.canvas.width;
            let gap = Math.random() * 75 + 125;
            let border = 60;
            let y = Math.random() * (game.canvas.height - gap - border) + gap + (border / 2);
            let pipe = new Pipe(x, y, this.pipeTexture, this.pipeSpeed, gap);
            game.addObject(pipe);
        }
    }
}

class Pipe extends SpriteObject {

    constructor(x, y, texture, speed, gap) {
        super("pipe", x, y, texture);

        this.speed = speed;
        this.gap = gap;
    }

    update(game, deltaTime) {
        super.update(game, deltaTime);

        this.x -= this.speed * deltaTime;
        if (this.x <= -this.width)
            this.remove();
    }

    /**
     *
     * @param {CanvasRenderingContext2D} ctx
     * @param deltaTime
     */
    render(ctx, deltaTime) {
        if (!this.ready) {
            super.render(ctx, deltaTime);
            return
        }

        ctx.drawImage(this.sprite, this.x, this.y);

        // ctx.fillRect(this.x - 20, this.y - this.gap, this.width + 40, this.gap);

        ctx.save();
        ctx.translate(0, this.y);
        ctx.scale(1, -1);
        ctx.drawImage(this.sprite, this.x, this.gap);
        ctx.restore();
    }

    /**
     *
     * @param {GameObject} other
     * @returns {boolean}
     */
    intersect(other) {
        let altY = this.y - this.height - this.gap;
        return (other.x <= this.x + this.width && other.x + other.width >= this.x) &&
            (
                (other.y <= this.y + this.height && other.y + other.height >= this.y) ||
                (other.y <= altY + this.height && other.y + other.height >= altY)
            );
    }

}
