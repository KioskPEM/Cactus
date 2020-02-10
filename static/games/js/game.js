class GameObject {

    /**
     * @param {String} name
     * @param {number} x
     * @param {number} y
     */
    constructor(name, x, y) {
        this.name = name;
        this.x = x;
        this.y = y;
        this.height = 1;
        this.width = 1;

        this.ready = true;
        this.removed = false;
    }

    remove() {
        this.removed = true;
    }

    /**
     *
     * @param {Game} game
     * @param {number} deltaTime
     */
    update(game, deltaTime) {
    }

    /**
     *
     * @param {CanvasRenderingContext2D} ctx
     * @param {number} deltaTime
     */
    render(ctx, deltaTime) {
        ctx.fillRect(this.x, this.y, this.width, this.height);
    }

}

class SpriteObject extends GameObject {

    /**
     *
     * @param {String} name
     * @param {number} x
     * @param {number} y
     * @param {String} texture
     */
    constructor(name, x, y, texture = null) {
        super(name, x, y);

        this.ready = false;
        this.sprite = new Image();
        this.sprite.onload = this.onImageLoaded.bind(this);
        this.sprite.src = texture != null ? texture : name;
    }

    onImageLoaded() {
        console.log(this.name, "is now ready");
        this.width = this.sprite.width;
        this.height = this.sprite.height;
        this.ready = true;
    }

    render(ctx, deltaTime) {
        if (this.ready)
            ctx.drawImage(this.sprite, this.x, this.y, this.sprite.width, this.sprite.height);
        else
            super.render(ctx, deltaTime);
    }


}

class SpriteSheetObject extends SpriteObject {

    constructor(name, x, y, texture, columns, rows, speed, offset, length) {
        super(name, x, y, texture);

        this.columns = columns;
        this.rows = rows;
        this.speed = speed;

        this.offset = offset != null ? offset : 0;
        this.length = length != null ? length : (columns * rows);
        this.maxFrames = columns * rows;

        this.time = 0;
        this.index = 0;
    }

    render(ctx, deltaTime) {
        if (this.ready) {

            this.time += deltaTime;
            if (this.time >= this.speed) {
                this.time = 0;

                this.index++;
                this.index %= this.length;
            }

            let frame = (this.offset + this.index) % this.maxFrames;
            let column = frame % this.columns;
            let row = Math.floor(frame / this.columns);

            this.width = this.sprite.width / this.columns;
            this.height = this.sprite.height / this.rows;

            let x = column * this.width;
            let y = row * this.height;

            ctx.drawImage(this.sprite, x, y, this.width, this.height, this.x, this.y, this.width, this.height);
        } else
            super.render(ctx, deltaTime);
    }

}

class Game {

    /**
     *
     * @param {HTMLCanvasElement} canvas
     */
    constructor(canvas) {
        this.canvas = canvas;
        this.canvas.width = 640;
        this.canvas.height = 410;

        this.context = canvas.getContext("2d", {
            alpha: false
        });
        this.context.imageSmoothingEnabled = false;

        this.running = false;
        this.ready = false;

        this.lastTime = 0;
        this.deltaTime = 0;
        this.objects = [];
    }

    reset() {
        this.context.fillStyle = "#FFFFFF";

        this.context.font = "50px sans-serif";
        this.context.textBaseline = "middle";
        this.context.textAlign = "center";
    }

    /**
     *
     * @param {FrameRequestCallback} time
     */
    update(time) {
        this.deltaTime = (time - this.lastTime) / 1_000;
        this.lastTime = time;

        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

        if (this.ready) {

            for (let i = this.objects.length - 1; i >= 0; i--) {
                let object = this.objects[i];
                object.update(this, this.deltaTime);
                object.render(this.context, this.deltaTime);
                if (object.removed) {
                    console.log(object.name, "removed");
                    this.objects.splice(i, 1);
                }
            }

        } else {

            this.context.fillText("LOADING...", this.canvas.width / 2, this.canvas.height / 2);
            this.updateState();

        }

        if (this.running)
            requestAnimationFrame(this.update.bind(this));
    }

    updateState() {
        for (let object of this.objects) {
            if (!object.ready) {
                this.ready = false;
                return;
            }
        }

        this.ready = true;
    }

    run() {
        if (this.running)
            return;

        this.reset();
        this.updateState();
        this.running = true;

        requestAnimationFrame(this.update.bind(this));
    }

    /**
     *
     * @param {GameObject} object
     */
    addObject(object) {
        this.ready &= object.ready;
        this.objects.push(object);
    }

    /**
     *
     * @param {GameObject} object
     */
    removeObject(object) {
        let index = this.objects.indexOf(object);
        this.objects.splice(index, 1);
    }

}
