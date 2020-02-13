class Bubble extends SpriteObject {

    constructor(name, x, y, texture, radius, score) {
        super(name, x, y, texture);

        this.xVel = (Math.random() * 200) - 100;
        this.yVel = -(30 + Math.random() * 15);

        this.radius = radius || ((Math.random() <= .01) ? this.maxSize : 20 + (Math.random() * 10));

        this.type = type || 'normal';
    }



}