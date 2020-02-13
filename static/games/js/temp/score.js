class Score {

    constructor() {
        let score = Cookies.get('score');
        this.value = parseInt(score) || 0;
        this.shouldDrawModifier = this.value > 0;

        this.text = undefined;

        this.scoreAnimation = 'none';

        this.bonusMultiplier = 1;
        this.bonusAnimation = 'none';

        this.penaltyMultiplier = 1;
        this.penaltyAnimation = 'none';

        this.reset();
    }

    update(deltaTime) {
        this.animationTimer -= deltaTime;
        if (this.animationTimer < 0) {
            this.animationTimer = 0;
            this.reset();
        }
    }

    draw(ctx, width, height) {
        let progressRatio = this.animationTimer / .25 - .5;
        let animationRatio = Math.cos(progressRatio * 4);

        // draw rectangle
        ctx.fillStyle = 'rgba(0, 0, 0, .2)';
        let rectHeight = (this.shouldDrawModifier ? 110 : 50);
        ctx.fillRect(0, (height / 2) - (rectHeight / 2), width, (rectHeight));

        // draw text
        this.drawScore(ctx, width, height, animationRatio, rectHeight);

        if (this.shouldDrawModifier)
            this.drawModifier(ctx, width, height, animationRatio, rectHeight);
    }

    drawScore(ctx, width, height, animationRatio, rectHeight) {
        let scoreAnimated = (this.scoreAnimation !== 'none');
        if (scoreAnimated)
            ctx.font = (40 + ((this.scoreAnimation === 'bigger' ? animationRatio : -animationRatio) * 16)) + 'px Open Sans';
        else
            ctx.font = '40px Open Sans';

        ctx.fillStyle = this.color;
        ctx.textBaseline = 'middle';
        ctx.textAlign = 'center';
        ctx.fillText(this.text ? this.text : this.value.toLocaleString(document.lang), width / 2, this.shouldDrawModifier ? (height / 2) - ((rectHeight / 4) - 10) : (height / 2));
    }

    drawModifier(ctx, width, height, animationRatio, rectHeight) {
        if (this.bonusMultiplier === this.maxBonusModifier) {
            ctx.font = 'bold 26px Open Sans';
        } else {
            let bonusAnimated = (this.bonusAnimation !== 'none');
            if (bonusAnimated)
                ctx.font = (20 + ((this.bonusAnimation === 'bigger' ? animationRatio : -animationRatio) * 5)) + 'px Open Sans';
            else
                ctx.font = '20px Open Sans';
        }

        ctx.fillStyle = '#2E7D32';
        ctx.fillText('x' + this.bonusMultiplier.toFixed(2), (width / 2) - 50, (height / 2) + (rectHeight / 4));

        if (this.penaltyMultiplier === this.maxPenaltyModifier) {
            ctx.font = 'bold 26px Open Sans';
        } else {
            let penaltyAnimated = (this.penaltyAnimation !== 'none');
            if (penaltyAnimated)
                ctx.font = (20 + ((this.penaltyAnimation === 'bigger' ? animationRatio : -animationRatio) * 5)) + 'px Open Sans';
            else
                ctx.font = '20px Open Sans';
        }

        ctx.fillStyle = '#C62828';
        ctx.fillText('x' + this.penaltyMultiplier.toFixed(2), (width / 2) + 50, (height / 2) + (rectHeight / 4));
    }

    reset() {
        this.color = '#FFFFFF';
        this.text = undefined;

        this.animationTimer = 0;
        this.scoreAnimation = 'none';
        this.bonusAnimation = 'none';
        this.penaltyAnimation = 'none';
    }

    addScore(value) {
        value = Math.floor(value);
        if (value > 0) {
            this.value += Math.floor(value * this.bonusMultiplier);
            this.text = undefined;

            this.shouldDrawModifier = true;

            this.color = '#FD2D75';
            this.scoreAnimation = 'bigger';
            this.animationTimer = .25;
        } else if (value < 0) {
            this.value += Math.floor(value * this.penaltyMultiplier);
            if (this.value <= 0) {
                this.value = 0;
                this.text = 'Game Over';

                this.shouldDrawModifier = false;
            } else {
                this.text = undefined;
                this.shouldDrawModifier = true;
            }

            this.color = '#880E4F';
            this.scoreAnimation = 'smaller';
            this.animationTimer = .25;
        }

        Cookies.set('score', this.value, {expires: 365});
    }

}

Score.prototype.maxBonusModifier = 3;
Score.prototype.maxPenaltyModifier = 2.25;