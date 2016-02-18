# Arbetsprov parkeringsavgift 2.1
Kopia på uppgiften finns [här](./Uppgift Parkeringsavgift 2.1.md)

## Om projektet
Jag valde att göra projektet i Laravel för att inte behöva uppfinna hjulet igen med deras ORM. Jag började med att försöka plocka ihop *bara de paket som jag behövde*. Ganska snabbt blev det ändå massor med paket så då valde jag tillslut att helt gå över till Laravel.

**Svenska** eftersom uppgiften var formad på svenska så kändes det naturligt att använda svenska. En slags markering att detta projekt enbart utgör en mycket liten del av en potentiell helhet. I ett *riktigt* sammanhang hade jag föredragit engelska före Svenska. Men jag tycker Svenskan tjänar ett syfte såhär när det är i form av en konkret och frikopplad uppgift.

### Förenklad beskrivning av förlopp
1. Ett parkeringsområde skapas med tillhörande regler och information
1. En användare skapas
1. Användaren startar sin parkering
  * Aktuell tidpunkt sparas
1. Användaren avslutar sin parkering
  * Jag valde i detta fallet att kalla på ett *"Jobb"* `app\Jobs\BeraknaKostnad.php`
  * Jobbet loopar igenom reglerna och perioden för parkeringen
  * Jobbet tar hänsyn till och ser om det finns specialregler som påverkas
  * Jobbet beräknar kostnaden
  * Jobbet lagrar data (`json`) från beräkningen detta används sedan vid arbetet med kvittot. Detta för att förbättra prestanda i fortsatt arbete. Det går givetvis att uppdatera/generera om beräkningen och dess data om så önskas.

### Vad ni ska titta på
* Överblick för datamodellerna finns i filen [Datamodeller.md](./Datamodeller.md)
* Datamodellerna och klasserna för projektet finns i `app\Models` (DB migrations finns i `migragrions`)
* TDD `tests\`
* `Jobs\BeraknaKostnad`
* För att se *"kvitto"* gå in på uri `/kvitto`

### Om min lösning
#### Problemidentifiering
**Specialregler**

Specialregler kändes som det mest kluriga. Inte att lösa det logiskt men att bygga så att det blir enkelt att lägga till nya specialregler i framtiden, alltså försöka separera den övergripande logiken med den väldigt specifika special-logiken. Därför försökte jag bygga specialreglerna så frikopplade från huvudprogrammet som möjligt. Därför har jag först en klass som heter **`Specialparkeringsregel`** den klassen pekar sedan vidare genom polymorphiska rellationer till de egentliga specialreglerna. Detta gör det väldigt enkelt att assosiera och ta bort specialregler till ett område.

All logik måste ske i denna specialregels-klass. Så klassen för att beräkna den första gratis timmen heter `ForstaTimmenXKr.php`


#### Områden som potentiellt behöver förbättras
**Tidsobjekt**

Just nu används 2 olika objekt för att hantera tid. De båda har stora fördelar men de båda behövs ändå för att smidigt få gjort jobbet. En förbättrningsåtgärd hade varit att skriva ihop en egen tids-klass som fyller alla behov och på så sätt skulle koden kunna bli väldigt mycket snyggare, effektivare och även mer lättförståelig.

**ExceptionHandeling**

Just nu har jag ingen exception handeling i projektet. Detta eftersom inmatningen ändå inte kan ske på fel sätt i denna miljö utan en "slutanvändare". Givetvis skulle det här behövas mer sådant vid en riktig implementation. Även tester för att bekräfta dessa exceptions hade också varit ett krav.

**Git**

I detta fall när det handlar om en uppgift där jag själv behöver bygga upp hela projektet själv ser jag inte att git kan tillämpas på ett givande sätt **
initialt**. Men när de grova ramarna är satta och förändringar i koden börjar ske. Det är då som Git verkligen blir starkt. Om man inte då skapar och bygger upp ett projekt tillsammans med andra, då måste man ju givetvis jobba på ett annat sätt.



