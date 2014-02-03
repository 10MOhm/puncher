function tronDraw() {

    var a = this.angle(this.cv)
        , sa = this.startAngle
        , sat = this.startAngle
        , ea
        , eat = sat + a
        , r = 1;

    this.g.lineWidth = this.lineWidth;

    this.o.cursor
        && (sat = eat - 0.3)
        && (eat = eat + 0.3);

    if (this.o.displayPrevious) {
        ea = this.startAngle + this.angle(this.v);
        this.o.cursor
            && (sa = ea - 0.3)
            && (ea = ea + 0.3);
        this.g.beginPath();
        this.g.strokeStyle = this.pColor;
        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
        if (this.shadow) {
            this.g.shadowColor = this.pColor;
            this.g.shadowBlur = 5;
        }
        this.g.stroke();
    }

    this.g.beginPath();
    this.g.strokeStyle = r ? this.o.fgColor : this.fgColor ;
    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
    if (this.shadow) {
        this.g.shadowColor = this.fgColor;
        this.g.shadowBlur = 5;
    }
    this.g.stroke();

    this.g.lineWidth = 2;
    this.g.beginPath();
    this.g.strokeStyle = this.o.fgColor;
    this.g.arc( this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
    if (this.shadow) {
        this.g.shadowColor = this.fgColor;
        this.g.shadowBlur = 5;
    }
    this.g.stroke();

    return false;
}
