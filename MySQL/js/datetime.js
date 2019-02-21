function ticken() {
    var year, month, day;
    var hour, minutes, seconds;
    var stundenZahl, minutenZahl, sekundenZahl;
    var heute = new Date();
    year = heute.getFullYear();
    month = heute.getMonth();
    day = heute.getDate();
    stundenZahl = heute.getHours();
    minutenZahl = heute.getMinutes();
    sekundenZahl = heute.getSeconds();
    hour = stundenZahl + ":";

    if (minutenZahl < 10)
        minutes = "0" + minutenZahl + ":";
    else
        minutes = minutenZahl + ":";
    if (sekundenZahl < 10)
        seconds = "0" + sekundenZahl + " ";
    else
        seconds = sekundenZahl + " ";
    zeit = day + "/" + (month + 1) + "/" + year + "|" + hour + minutes + seconds + " Uhr";
    uhr.innerHTML = zeit;
    window.setTimeout("ticken();", 1000);
}
window.onload = ticken;


