
MML:n kartat HTML5-geolocation -sovelluksena
============================================

Sovellus näyttää Maanmittauslaitoksen kartan tai ilmakuvan käyttäjän sijainnista. Käyttäjä voi valita kartan tyypin (peruskartta, taustakartta tai ortokuva (ilmakuva)), mittakaavan (kartan halkaisija 1, 2 tai 4 km) sekä laadun (jpg/pieni tiedosto tai png/suuri tiedosto). Sovellus muistaa tehdyt valinnat (kunnes sivu ladataan uudelleen).

Kartat haetaan http://kartat.kapsi.fi/ -palvelun WMS-rajapinnasta.

Karttasivu käyttää jQuerya: aseta polku index.php-tiedostoon.

Testattu:
- Android 2.3, perusselain: ok
- iPad 2, perusselain: ok
- Nokia 700, perusselain: ei toimi, karttakuva tyhjä ja tarkkuus undefined
- Nokia Lumia 500, perusselain: ei toimi, karttakuva tyhjä ja tarkkuus undefined
- Windows Phone Emulator 7.1: ok
- Chrome 22: ok
- Firefox 16: ok

