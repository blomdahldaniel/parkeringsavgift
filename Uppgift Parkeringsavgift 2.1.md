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


# Kommentarer
### Generering av parkerings-taxa
Istället för att generera taxan som ett svar från en metod, varje gång man behöver taxan, skulle man istället kunna anropa event då sluttiden rapporteras in som sammanställer taxan och sen lägger in värdet in i parkeringsobjektet och lagra i databasen. Detta beroende på hur prestandan

### Begrepp
Eftersom jag inte är helt inne i branschen (än..) så inser jag att några av mina val av begrepp eller klassnamn kanske kan vara lite off. Jag valde också aktivt att skriva projektet på Svenska denna gången då det känns som om det passar sig bättre i ett *test* eller *uppgift*
