# Aggressive Cows
## Ievads
Šajā projektā ir risinājums [Aggressive Cows problēmai](https://www.spoj.com/problems/AGGRCOW/). Rakstīts PHP8.4, risinājumam neizmantojot nekādas bibliotēkas (testēšanai gan izmantoju).

## SPOJ problēma
Īsumā, tā ir sekojoša: ir dots skaits ar govīm, un ir dotas aizgaldu pozīcijas. Jāizrēķina, kāda ir lielākā distance starp blakusesošām govīm, kādu var sasniegt ar atbilstošu izvietojumu pa aizgaldiem.

## Risinājuma būtība
#### Distances iespējamības tests
Dotai distancei risinājums nosaka, vai tā ir iespējama, vai nav. Šis tests vienkārši katru nākamo govi ievieto tuvākajā atļautajā aizgaldā.

#### Gala risinājums
Ar bināro meklēšanu mēģina sameklēt lielāko iespējamo distanci.

## Darbināšana

Kaut arī bibliotēkas izmantotas tikai testos, arī vienkārši darbināšanai ir vajadzīgs composer autoloaderis, kurš tiek izveidots, darbinot:
```
composer install
```

Programma neizvada nekādus uzaicinājumus. Programma sagaida tādu ievadu, kāds ir aprakstīts SPOJ problēmas nostādnē, proti:
* pirmajā rindā tā sagaida piemēru skaitu P, P >= 1
* katram piemēram
  * pirmajā rindā tā sagaida aizgaldu skaitu A un govju skaitu C, 2 <= C <= A <= 100 000
  * nākamajās A rindās tā sagaida aizgaldu koordināti K, 0 <= K <= 1 000 000 000

Lietošanas piemērs:
```
php aggressive_cows.php
2
5 3
1
2
8
4
9
4 3
10
1000
1005
15
```

Izvads, jaunā rindā katram piemēram:
```
3
5
```

## Darbināšana SPOJ
Tā kā SPOJ ļauj izmantot vienīgi PHP7.3 un ļauj pievienot tikai vienu failu, tad ērtībai [iekļauju failu, kurš sagatavots iesniegšanai SPOJ](./aggressive_cows_one_file_php73.php). Tajā, atbilstoši PHP7.3
* klašu konstantēm nav typehinti
* klašu mainigajiem nav typehinti
* saīsinātie konstruktori aizstāti ar garo formu
* nav izmantoti _named parameters_
* nav izmantotas _arrow functions_

## Testēšana
Testiem izmantoju [Pest](https://pestphp.com/).

Sākumā instalējam vajadzīgās bibliotēkas.
```
composer install
```
Pēc tam varam darbināt testus:
```
composer test
```
