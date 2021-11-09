$(window).resize(function() {
    let w = $('#banner').width() - 20;
    let h = w * 417.273 / 540;
    if (h > $('#banner_container').height() - 20) {
        h = $('#banner_container').height() - 20;
        w = h * 540 / 417.273;
    }
    $('#canvas').css('width', w);
    $('#canvas').css('height', h);
    $('#paper').css('transform', 'scale(' + w / 540.0 * 0.511364 + ')');
});
$(document).ready(function() {
    $(".certificate-block").css("visibility", "hidden");
    $("#drawcanvas").css("visibility", "hidden");
    draw();
});
$(window).on('load', function() {
    let w = $('#banner').width() - 20;
    let h = w * 417.273 / 540;
    if (h > $('#banner_container').height() - 20) {
        h = $('#banner_container').height() - 20;
        w = h * 540 / 417.273;
    }
    $('#canvas').css('width', w);
    $('#canvas').css('height', h);
    $('#paper').css('transform', 'scale(' + w / 540.0 * 0.511364 + ')');
    $(".certificate-block").css("visibility", "visible");
    $("#drawcanvas").css("visibility", "visible");
})

function draw() {
    canvas = document.getElementById('drawcanvas');
    let w = $('#canvas').width();
    let h = $('#canvas').height();
    let s = $('#canvas').width() / 540.0 * 0.511364;
    ctx = canvas.getContext('2d');
    ctx.scale(w / 2000 / s, h / 1540 / s);

    ctx.lineWidth = 150;
    ctx.strokeStyle = '#565656';
    ctx.strokeRect(0, 0, 2000, 1540);

    let txt = $('#certify_tokennumber').text() + ' ';
    let font = ['italic 36px serif', 'bold 36px serif', '36px serif'];

    let offx = 45;
    let nFont = 0;
    let nSide = 0;


    let cx = 0;
    let cp = 0;
    let i = 0;
    let j = 0;
    let width = 2000 - offx;

    ctx.font = font[nFont];
    let x = offx + ctx.measureText(txt).width;

    let y = 50;

    ctx.textAlign = 'left';
    ctx.textBaseline = 'alphabetic';
    ctx.fillStyle = '#969696';

    while (1) {
        cx = ctx.measureText(txt[cp]).width;
        if (x + cx > width) {
            ctx.translate(nSide % 2 == 0 ? 2000 / 2 : 1540 / 2, nSide % 2 == 0 ? 1540 / 2 : 2000 / 2);
            ctx.rotate(Math.PI / 2);
            ctx.translate(nSide % 2 == 0 ? -1540 / 2 : -2000 / 2, nSide % 2 == 0 ? -2000 / 2 : -1540 / 2);

            nSide++;
            width = nSide % 2 == 0 ? 2000 - offx : 1540 - offx;
            x = offx;
        }
        ctx.fillText(txt[cp], x, y);
        x += cx;
        cp++;
        if (cp >= txt.length) {
            cp = 0;
            if (nSide > 3) break;
            nFont++;
            if (nFont > 2) nFont = 0;
            ctx.font = font[nFont];
        }
    }

    ctx.fillStyle = '#0a71ff';
    ctx.fillRect(0, 705, 140, 131);
    ctx.fillRect(1861, 705, 140, 131);

    const image = new Image(58, 233); // Using optional size for image
    image.onload = drawImageActualSize; // Draw when image has loaded

    image.src = 'hold.jpg';

    function drawImageActualSize() {
        ctx.drawImage(this, 138, 656);
        ctx.translate(2000 / 2, 1540 / 2);
        ctx.rotate(Math.PI);
        ctx.translate(-2000 / 2, -1540 / 2);
        ctx.drawImage(this, 138, 655);
    }
}