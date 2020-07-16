function countdown(field)
{
    var date = new Date();
    var event_date = new Date(<?= $event_date ?>);
    var time = (event_date.getTime() - date.getTime()) / 1000; 
    var second = Math.floor(time % 60);
    time = time / 60;
    var minute = Math.floor(time % 60);
    time = time / 60;
    var hour = Math.floor(time % 24);
    var day = Math.floor(time / 24);
    if (time < 0) {
        field.textContent = 'compte a rebours termine';
    } else {
        field.textContent = day + 'j ' + hour + 'h ' + minute + 'm ' + second + 's';
        setTimeout(countdown, 1000, field);
    }
}