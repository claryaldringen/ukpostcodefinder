Několik poznámek k aplikaci a kódu
==================================

 - Aplikace je funkční.
 - Test funkční není - nepodařilo se mi donutit aplikaci aby spouštěla jen command a ignorovala v testu příkazovou řádku,
 nicméně si myslím, že to je jen otázkou hledání v dokumentaci a času (který se mi teď bohužel neodstává).
 - Test je napsaný pro pozitivní příklad, kdy vše funguje jak má. Testy pro negativní případy by vypadaly podobně, 
 testoval bych tam špatný vstup, nedostupnost zdroje, prázdný návratový dataset apod.
 - Komentáře jsou pouze u metod, které jsem psal já. Obecně se snažím psát spíš čitelnější kód než to pak dohánět komentáři (čitelný kód je nejepresivnější vyjádření algoritmu).
 - Někdo dává na začátek private a protected proměnných podtržítko, já to nedělám s tím, že každé moderní ide vývojáři znázorní, zda se jedná a private, protected nebo public.
 - Pravidla ohledně komentářů a proměnných jsem si nevycucal z prstu, ale vycházím z knihy Rickyho C. Matina Čistý kód. 
 V případě potřeby nemám problém přejít na jiný code standard (např. v LS jsme ta podtržítka používali).