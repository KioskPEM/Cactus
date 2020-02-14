class Bubble extends SpriteObject {

    constructor(name, x, y, texture, radius, score) {
        super(name, x, y, texture);

        this.xVel = (Math.random() * 200) - 100;
        this.yVel = -(30 + Math.random() * 15);

        this.radius = radius || ((Math.random() <= .01) ? this.maxSize : 20 + (Math.random() * 10));

        this.type = type || 'normal';
    }



}

class BubbleSpawner extends GameObject {

    /**
     *
     * @param {number} spawnDelay
     * @param {string} pipeTexture
     * @param {number} pipeSpeed
     */
    constructor(spawnDelay, pipeTexture, pipeSpeed) {
        super("bubble_spawner", 0, 0);

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