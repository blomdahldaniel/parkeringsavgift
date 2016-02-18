# Arbetsprov parkeringsavgift 2.1

### Krav:
* PHP

### UPPGIFT
Skriv ett projekt som räknar ut den totala parkerings-avgiften från en tidpunkt till en annan tidpunkt. Det behöver INTE byggas HTML-sidor för att t.ex. göra inmatningar via en webbsida, utan det räcker med PHP-kod.

Taxan på parkeringsplatsen är:

> **Alla dagar 09:00 - 18:00:** 5 kr / tim

> **Övrig tid:** 0 kr / tim

> **Max pris per dygn:** 25 kr

**Första timmen (första timmen som inte är 0 kr / tim):** 10 kr / tim (därefter 5 kr / tim enligt ovan)

Tidpunkter anges med unix timestamp och skall kunna anges med flera dygns skillnad.

**Till exempel:**  Idag 10:00 -> Idag 12:00 skall bli en total kostnad på 15 kr. (Första timmen 10 kr / tim +
därefter 5 kr / tim).
