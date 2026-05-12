# SideQuestJar Dokumentáció – Részletes Vizsgadokumentáció

## Tartalomjegyzék
1. [Bevezetés, a téma ismertetése, témaválasztás indoklása, szakmai célkitűzés](#1-bevezetés-a-téma-ismertetése-témaválasztás-indoklása-szakmai-célkitűzés)
2. [Fejlesztői dokumentáció](#2-fejlesztői-dokumentáció)
    - 2.1 [Fejlesztőkörnyezet ismertetése és indoklása](#21-fejlesztőkörnyezet-ismertetése-és-indoklása)
    - 2.2 [Kialakított adatszerkezet bemutatása](#22-kialakított-adatszerkezet-bemutatása)
    - 2.3 [Érdekes és egyedi algoritmusok](#23-érdekes-és-egyedi-algoritmusok)
    - 2.4 [Tesztdokumentáció](#24-tesztdokumentáció)
    - 2.5 [Fejlesztési lehetőségek](#25-fejlesztési-lehetőségek)
3. [Felhasználói dokumentáció](#3-felhasználói-dokumentáció)
    - 3.1 [A program célja és lényegesebb funkciói](#31-a-program-célja-és-lényegesebb-funkciói)
    - 3.2 [Szükséges hardver- és szoftvereszközök](#32-szükséges-hardver--és-szoftvereszközök)
    - 3.3 [Telepítés és indítás lépései](#33-telepítés-és-indítás-lépései)
    - 3.4 [A program részletes bemutatása](#34-a-program-részletes-bemutatása)
    - 3.5 [Hibajelzések és helytelen használat](#35-hibajelzések-és-helytelen-használat)
    - 3.6 [Információkérés lehetőségei](#36-információkérés-lehetőségei)
4. [Összefoglalás, köszönetnyilvánítás](#4-összefoglalás-köszönetnyilvánítás)
5. [Irodalomjegyzék](#5-irodalomjegyzék)

---

## 1. Bevezetés, a téma ismertetése, témaválasztás indoklása, szakmai célkitűzés

A mai felgyorsult, digitális és gyakran ingergazdag világban az emberek jelentős része küzd a motiváció hiányával, a halogatással (prokrasztináció) és azzal, hogy értelmes, hosszú távon is fenntartható napi rutinokat alakítson ki. Sokan élik az életüket egyfajta "robotpilóta" üzemmódban, ahol a munkanapok vagy iskolai napok egyhangúan összefolynak, és hiányzik belőlük az újdonság varázsa, a spontaneitás, vagy éppen a kisebb kihívások adta azonnali sikerélmény. Erre a komplex, modern kori problémára keresett újszerű, technológiai alapú megoldást csapatunk (Karácsony Zoltán, Borza Gergő, Nagy Panna Viktória) a **SideQuestJar** nevű szoftveres platform megalkotásával.

A SideQuestJar egy innovatív, gamifikált (játékosított) webes és mobilalkalmazás. Alapvető pszichológiai célja, hogy játékelemekkel (pontozás, sorozatok, közösségi interakciók), mikro-kihívásokkal és azonnali vizuális visszajelzésekkel motiválja a felhasználókat arra, hogy kiszakadjanak a hétköznapi rutinból. Az alkalmazás alapkoncepciója az „ötletdoboz" vagy „kívánságüveg" (Jar) metaforájára épül. Ezekbe a digitális dobozokba a felhasználók különböző feladatokat, úgynevezett „küldetéseket" (Quest) gyűjthetnek. A rendszer logikája naponta véletlenszerűen sorsol egy feladatot az adott dobozból, amelyet a felhasználónak aznap 24 órán belül teljesítenie kell. A sikeres teljesítésért és annak fotóval történő bizonyításáért a rendszer virtuális jutalmakat ad: tapasztalati pontokat (XP) biztosít, a folyamatos napi aktivitást pedig az úgynevezett Streak (napi sorozat) számláló növelésével díjazza.

Bár a jelenlegi szoftverpiacon (Google Play, App Store, webalapú SaaS szolgáltatások) már számos feladatkezelő (to-do list) és szokáskövető alkalmazás létezik – mint például a Habitica, a Todoist, a TickTick vagy a Streaks –, ezek túlnyomó többsége a szigorú produktivitásra, a munkahelyi hatékonyságra és a statikus feladatlisták monoton kipipálására fókuszál. A mi szoftverünk mibenléte és egyedisége abban rejlik, hogy sokkal inkább a spontaneitásra, a meglepetés erejére és a közösségi élményre épít. Nálunk nem egy előre megírt, szorongást keltő "to-do" listát kell végigcsinálni, hanem a felhasználó egy mindennapi kalandban (Side Quest) vesz részt. Ezt az élményt barátaival is megoszthatja, saját "Jar"-okat hozhatnak létre közösen (például egy baráti társaság közös hétvégi kihívásaihoz), és egy közösségi üzenőfalon (Proof Feed) keresztül motiválhatják, "like"-olhatják egymás képekkel dokumentált teljesítéseit. A szoftver így átmenetet képez egy produktivitási eszköz és egy pozitív megerősítésre épülő közösségi hálózat között.

A témaválasztást komoly személyes érintettség is indokolta. Fiatal felnőttként és informatikus hallgatóként mindannyian tapasztaltuk már a kiégés (burnout) korai jeleit, amikor a vizsgaidőszakok vagy a napi fejlesztési feladatok monotonitása rányomta a bélyegét a mentális egészségünkre és a hangulatunkra. Szükségünk volt egy digitális eszközre, ami kiszakít a képernyő és a tankönyvek fogságából, és egy kis játékosságot csempész a kötelességek közé – legyen az egy "Igyál meg 3 liter vizet", "Rajzolj egy macskát", vagy "Sétálj 30 percet a friss levegőn" típusú küldetés. 

A projekt megkezdésekor szigorú **szakmai célkitűzéseket** is megfogalmaztunk, amelyek a következők voltak:
1. **Modern Architektúra elsajátítása:** Egy teljes értékű, vállalati (enterprise) szintű technológiákon nyugvó, RESTful architektúrára épülő, elosztott rendszer létrehozása.
2. **Backend robusztusság:** A Laravel keretrendszer mélyreható megismerése, különös tekintettel az ORM (Object-Relational Mapping), a middleware-ek, a jogosultságkezelés (Policies) és a végpontok (API) védelmére (Sanctum).
3. **Frontend reaktivitás:** A modern, SPA (Single Page Application) jellegű felhasználói élmény biztosítása a Livewire technológia segítségével, elkerülve a hagyományos, teljes oldalújratöltéssel járó kéréseket.
4. **Adatbázis-optimalizálás:** Összetett, több-több (Many-to-Many) és polimorfikus (Polymorphic) kapcsolatok tervezése és hatékony lekérdezése relációs adatbázisban, szem előtt tartva a skálázhatóságot.
5. **Projektmenedzsment és verziókövetés:** A csapatmunkában elengedhetetlen Git/GitHub munkafolyamatok (branching, merging, konfliktuskezelés) gyakorlati alkalmazása.

A célunk tehát nem pusztán egy beadandó feladat elkészítése volt, hanem egy olyan "Minimum Viable Product" (MVP) letétele az asztalra, amely megállja a helyét egy fejlesztői portfólióban, és valós piaci igényeket képes kielégíteni modern, letisztult formában.

---

## 2. Fejlesztői dokumentáció

Ez a fejezet a szoftver technikai mélységeit tárgyalja, bemutatva az architektúrát, az adatszerkezetet, a specifikus algoritmusokat és a minőségbiztosítási (tesztelési) folyamatokat. A célközönség szoftvermérnökök, IT szakemberek és a vizsgáztató bizottság tagjai.

### 2.1 Fejlesztőkörnyezet ismertetése és indoklása

A SideQuestJar egy komplex kliens-szerver (Client-Server) architektúrájú alkalmazás. A rendszer alapvetően két nagy logikai részre bontható: a böngészőben futó felületre (Frontend) és az üzleti logikát, valamint az adatbázis-műveleteket végző szerveroldalra (Backend). A technológiai stack kiválasztásánál az iparági standardokat, a hosszú távú támogatottságot (LTS) és a fejlesztés sebességét tartottuk szem előtt.

**Szerveroldali környezet (Backend)**
A projekt gerincét a **PHP 8.2+** programozási nyelv és a **Laravel 12.x** keretrendszer adja. 
- *Miért a Laravel?* Bár választhattunk volna Node.js-t (Express) vagy Pythont (Django), a Laravel MVC (Model-View-Controller) architektúrája kiemelkedő produktivitást biztosít. A beépített "out-of-the-box" funkciói, mint például a migrációk (adatbázis séma verziókövetése), az Eloquent ORM (amely objektumorientált megközelítést ad az SQL lekérdezésekhez), a robusztus routing rendszer és az előre konfigurált autentikációs modulok hónapokkal rövidítették le a fejlesztési időt.
- *Biztonság és API:* A Laravel natívan biztosít CSRF (Cross-Site Request Forgery) védelmet a webes űrlapokhoz. Mivel az alkalmazás tervezett jövője egy natív mobil kliens (pl. iOS/Android app) kiszolgálása, az architektúrát RESTful elvek alapján építettük fel (`routes/api.php`). A mobil kliensek hitelesítéséhez a **Laravel Sanctum** csomagot integráltuk, amely egy pehelysúlyú, token-alapú (Bearer token) hitelesítési rendszert nyújt, tökéletesen elszeparálva a webes session-öktől.

**Kliensoldali környezet (Frontend)**
A felhasználói felület (UI) megjelenítéséért és a böngészőbeli interakciókért felelős réteg.
- *Livewire 4:* Ez a technológia jelenti a legnagyobb technikai innovációt a projekt webes felületén. Ahelyett, hogy egy teljesen különálló frontend keretrendszert (pl. React vagy Vue.js) építettünk volna, amely API hívásokkal kommunikál a szerverrel, a Livewire-t választottuk. A Livewire lehetővé teszi dinamikus, reaktív komponensek írását PHP-ben. A háttérben AJAX kérésekkel kommunikál a szerverrel, frissítve a DOM-ot (Document Object Model) oldalújratöltés nélkül. Ez az SPA-szerű (Single Page Application) élmény tökéletes folyamatosságot biztosít a felhasználónak, miközben a fejlesztői csapat a megszokott Laravel környezetben maradhatott.
- *Bootstrap 5 és SCSS:* A reszponzív (minden képernyőmérethez igazodó) design alapját a Bootstrap 5 adja. Ezt azonban nem hagytuk alapértelmezett állapotban: a `resources/css` és `resources/scss` mappákban egyedi sötét témát (Dark Mode) alakítottunk ki. Az SCSS (Sass) pre-processzor segítségével változókat használtunk a színekhez (pl. `$bg-block-blue`, `$pink-button`), ami globális és egységes stílusmódosításokat tett lehetővé. A CSS/JS fájlok modern aszinkron betöltéséről és minimalizálásáról a **Vite** build eszköz gondoskodik.

**Adatbázis-kezelés**
- A helyi fejlesztői környezetben és a tesztelés során a könnyű hordozhatóság miatt **SQLite** fájlalapú adatbázist használtunk.
- Az éles (Production) környezethez a szoftver teljes mértékben fel van készítve a **MySQL 8** (vagy MariaDB) futtatására. Az adatbázis motorok közötti váltást a Laravel Eloquent absztrakciós rétege teszi teljesen zökkenőmentessé: egyetlen SQL kódot sem kellett újraírnunk.

**Fejlesztőeszközök és Hardverkövetelmények**
A szoftver fejlesztése nagyrészt Windows 11 és macOS operációs rendszereken történt, Visual Studio Code és PhpStorm integrált fejlesztői környezetek (IDE) használatával. A lokális webszervert a Laravel beépített `artisan serve` parancsa, valamint XAMPP szolgáltatta. A hardverigény a fejlesztéshez egy modern többmagos processzor (Intel i5/M1) és 8-16 GB RAM volt.
A forráskód verziókövetését **Git** segítségével, egy megosztott **GitHub** tárolóban végeztük, ahol a csapat tagjai külön branch-eken (pl. `feature/chat`, `bugfix/login`) dolgoztak, és Pull Request-ek segítségével fésülték össze (merge) a munkát.

### 2.2 Kialakított adatszerkezet bemutatása

A szoftver relációs adatbázisának felépítése szorosan követi az entitás-kapcsolat (ER) modell alapelveit. Az adatbázis több mint 10 táblát tartalmaz, amelyek normalizált formában tárolják az adatokat az adatredundancia elkerülése végett. Minden tábla `utf8mb4` karakterkódolást használ a nemzetközi karakterek és hangulatjelek (emoji) hibátlan tárolása érdekében.

![Kép helye: Adatbázis relációs modellje (ER Diagram placeholder, részletes vizuális kapcsolati hálóval)]()

A legkritikusabb táblák részletes szerkezete:

**1. `users` (Felhasználók)**
Az alkalmazás központi entitása. Minden más tábla közvetve vagy közvetlenül kapcsolódik ide.
- `id` (unsigned big integer, Primary Key, Auto Increment)
- `username` (varchar 255, Unique): Login név és megjelenített név. Indexelve a gyors kereséshez a barát-felvételnél.
- `fullname` (varchar 255): A felhasználó polgári neve.
- `email` (varchar 255, Unique): Regisztrációs email cím.
- `email_verified_at` (timestamp, Nullable): Az email megerősítésének időpontja.
- `password` (varchar 255): Bcrypt algoritmussal hash-elt jelszó (soha nem tárolunk jelszót egyszerű szövegként).
- `birth_date` (date): Születési idő a 16 éves korhatár validációjához.
- `profile_picture` (varchar 255): Relatív elérési út a `storage/app/public` mappában lévő profilképhez.
- `is_admin` (boolean, Default 0): Jogosultsági szintet meghatározó flag.
- `user_xp` (integer, Default 0): A megszerzett tapasztalati pontok összesítése.
- `created_at`, `updated_at` (timestamp): Laravel alapértelmezett időbélyegek.
- `deleted_at` (timestamp, Nullable): *Soft Delete* mező. Ha egy felhasználó törli magát, a rekord itt kap egy időbélyeget, fizikai törlés helyett, így a referenciális integritás nem sérül (posztjai, like-jai megmaradnak, de ő nem tud belépni).

**2. `jars` (Ötletdobozok)**
Egy adott kihívásgyűjteményt reprezentál.
- `id` (unsigned bigint, PK)
- `name` (varchar 255): A Jar elnevezése.
- `description` (text, Nullable): Részletes tájékoztató a Jar céljáról.
- `picture` (varchar 255, Nullable): Borítókép.
- `type` (enum): A Jar jogosultsági modellje. Értékek: `'public_jar'`, `'private_jar'`, `'friends_only_jar'`, `'invite_only_jar'`, `'official_jar'`.
- `created_by` (unsigned bigint, FK -> `users.id`): A létrehozó usert azonosítja (Cascade delete on user delete - opcionális, a soft delete miatt inkább megtartjuk az adatot).
- `current_daily_quest_id` (unsigned bigint, Nullable, FK -> `jar_quests.id`): Az aznapra kisorsolt feladat.
- `previous_daily_quest_id` (unsigned bigint, Nullable, FK -> `jar_quests.id`): A tegnapi feladat. Ezzel akadályozzuk meg, hogy a sorsoló algoritmus két egymást követő napon ugyanazt a feladatot adja.
- `current_daily_quest_date` (date, Nullable): A sorsolás napja. Ennek segítségével tudja a rendszer eldönteni, hogy mikor kell újrasorsolni.

**3. `jar_quests` (Küldetések)**
A Jar-okhoz tartozó egyedi feladatok entitása. (One-to-Many kapcsolat a Jars táblával).
- `id` (unsigned bigint, PK)
- `jar_id` (unsigned bigint, FK -> `jars.id`, Cascade on Delete)
- `title` (varchar 255): A feladat rövid, frappáns címe.
- `description` (text): Részletes instrukciók a feladathoz.
- `picture` (varchar 255, Nullable): Szemléltető kép a feladathoz.
- `xp_gain` (integer, Default 10): Dinamikusan állítható jutalom. Nehezebb feladatok több pontot érhetnek.

**4. `quest_completions` (Teljesítések / Proof Feed posztok)**
Amikor egy felhasználó elvégez egy feladatot, ebbe a táblába kerül egy bejegyzés. Ez szolgál a főoldali Feed alapjául.
- `id` (unsigned bigint, PK)
- `user_id` (unsigned bigint, FK -> `users.id`)
- `jar_quest_id` (unsigned bigint, FK -> `jar_quests.id`)
- `completion_date` (datetime): A posztolás pontos mikorja.
- `picture` (varchar 255, Nullable): Bizonyíték kép (Proof). E nélkül nem ad a rendszer xp-t (üzleti szabály).
- `type` (enum): Poszt láthatósága ('public_post', 'private_post', 'friends_only_post'). Független a Jar láthatóságától.

**5. `jar_users` (Jar tagok - Pivot tábla)**
Kapcsolótábla a Many-to-Many kapcsolathoz a Felhasználók és a Jarok között.
- `jar_id` (unsigned bigint, FK -> `jars.id`)
- `user_id` (unsigned bigint, FK -> `users.id`)
- `streak` (integer, Default 0): Adatbázis szintű számláló. Ez mutatja, hogy az adott felhasználó hány egymást követő napon teljesített feladatot az adott Jar-ban. Ha kihagy egy napot, egy időzített feladat (Cron/Scheduler) 0-ra állítja.

**6. `likes` (Polimorfikus tábla)**
A rendszer egyik legkifinomultabb szerkezeti eleme. Mivel a projektben Jar-okat, Questeket és Teljesítéseket (Completions) is lehet "like"-olni, nem hoztunk létre minden entitásnak külön kapcsolótáblát.
- `id` (unsigned bigint, PK)
- `user_id` (unsigned bigint, FK -> `users.id`)
- `likeable_type` (varchar 255): A kedvelt modell osztályneve (pl. `App\Models\Jar` vagy `App\Models\JarQuest`).
- `likeable_id` (unsigned bigint): A kedvelt entitás azonosítója a saját táblájában.
- *Egyedi összetett index:* A `(user_id, likeable_type, likeable_id)` hármasra egyedi indexet tettünk, hogy egy felhasználó ne tudjon egy dolgot kétszer kedvelni az adatbázis szintjén sem.

**7. `friends` (Baráti kapcsolatok)**
Két felhasználó közötti kétirányú kapcsolat tárolására.
- `user_id` (unsigned bigint, FK -> `users.id`): A kezdeményező fél.
- `friend_id` (unsigned bigint, FK -> `users.id`): A célpont.
- `status` (enum): `'pending'` (várakozó) vagy `'accepted'` (elfogadva). Az elutasított kérések törlődnek a táblából.

**8. További kiegészítő táblák:** 
- `messages`: A felhasználók közötti valós idejű chat üzenetek tárolására (küldő, fogadó, üzenet szövege, időbélyeg, olvasottsági státusz).
- `jar_invitations`: Privát meghívók kezelésére. Tartalmaz egy `status` mezőt és az időbélyegeket, amivel a rendszer 5 perces lejárati időt tud kalkulálni.
- `personal_access_tokens`: A Sanctum által használt API authentikációs tokenek táblája.

### 2.3 Érdekes és egyedi algoritmusok

A szoftver fejlesztése során számos olyan problémával szembesültünk, amelyek megoldása túlmutatott a keretrendszer alapvető CRUD (Create, Read, Update, Delete) műveletein. Az alábbiakban bemutatjuk a legérdekesebb algoritmusainkat, pseudo-kóddal és technikai magyarázattal kísérve.

**1. Napi Quest Rotációs Algoritmus (Daily Draw Logic)**
*Helyszín: `App\Http\Controllers\JarController::getOrCreateDailyQuest`*
A projekt központi motorja. A cél az, hogy egy Jar-hoz naponta egy, és csakis egy új feladat kerüljön kiválasztásra úgy, hogy tegnapelőttit újra kaphatja a felhasználó, de tegnapit nem (kivéve, ha csak 1 feladat van összesen). Mindezt úgy kellett megoldani, hogy ne generáljunk felesleges adatbázis írásokat minden oldalfrissítéskor.

*Logika:*
1. A kliens lekéri az aktuális Jar-t.
2. A backend megvizsgálja a Jar `current_daily_quest_date` mezőjét.
3. **Ha** a dátum megegyezik a szerver mai dátumával (UTC szerint), akkor nincs teendő, visszaadja a `current_daily_quest_id`-hoz tartozó Quest objektumot.
4. **Ha** a dátum régebbi (vagy null, mert új a Jar):
    a. Lekéri a Jar-hoz tartozó összes quest ID-t egy tömbbe.
    b. Ellenőrzi a tömb méretét. Ha csak 1 quest van, azt adja vissza és frissíti a dátumot.
    c. Ha több quest van, lefuttat egy SQL lekérdezést: `SELECT * FROM jar_quests WHERE jar_id = X AND id != [previous_daily_quest_id] ORDER BY RAND() LIMIT 1`.
    d. Az újonnan sorsolt quest ID-ját beállítja a Jar `current_daily_quest_id` mezőjébe.
    e. A régi `current_daily_quest_id` értékét átmozgatja a `previous_daily_quest_id` mezőbe.
    f. A dátumot frissíti `Carbon::now()->format('Y-m-d')`-re.
    g. Adatbázis tranzakció mentése (`$jar->save()`).

Ez a lusta (lazy-evaluated) sorsolási algoritmus rendkívül erőforráskímélő, mert csak akkor sorsol, ha valaki megnyitja a Jar-t aznap először. Nincs szükség éjszaka futó nehézkes háttérfolyamatokra (Cron jobs), amik több tízezer Jar esetén túlterhelnék a szervert.

**2. Polimorfikus "Toggle" (Kapcsoló) Rendszer a Kedvelésekhez**
*Helyszín: `App\Http\Controllers\LikeController`*
Amikor egy felhasználó rákattint a szív ikonra egy Jar-nál vagy egy Posztnál, a háttérben egy aszinkron AJAX kérés indul. A kihívás a gyors válaszidő (latency) és az adatintegritás volt.

*Logika:*
```php
public function toggleQuest(JarQuest $quest): JsonResponse
{
    $userId = Auth::id();
    // 1. Megpróbáljuk megkeresni az adatbázisban a meglévő kedvelést.
    $existingLike = $quest->likes()->where('user_id', $userId)->first();

    if ($existingLike){
        // 2. Ha már létezik, akkor a felhasználó "unlike"-olni akar. Töröljük a rekordot.
        $existingLike->delete();
        return response()->json([
            'message' => 'Quest unliked',
            'liked' => false,
            'likes_count' => $quest->likes()->count(), // Visszaadjuk az új összesített számot
        ]);
    }

    // 3. Ha nem létezik, létrehozzuk. A polimorfikus metódus automatikusan 
    // beállítja a likeable_type-ot 'App\Models\JarQuest'-re.
    $quest->likes()->create(['user_id' => $userId]);

    return response()->json([
            'message' => 'Quest liked',
            'liked' => true,
            'likes_count' => $quest->likes()->count(),
    ]);
}
```
Ez a megoldás azért elegáns, mert egyetlen API végpont (endpoint) kezeli a feliratkozást és a leiratkozást is, a kliensnek (Javascript/Livewire) csak a frissített UI állapotot (piros szív vagy üres szív) kell lekezelnie a kapott JSON válasz alapján.

**3. Biztonságos Képvágás és Base64 Feldolgozás (Image Cropping Pipeline)**
A felhasználók által feltöltött képek (profilképek, bizonyítékok) hatalmas méretűek lehetnek (pl. egy 108 MB-os telefonos panorámakép), ami hamar megtöltené a szerver tárhelyét, és lassítaná az oldal betöltését. A nyers fájlfeltöltés helyett egy kétlépcsős, elosztott algoritmust írtunk.

*Logika:*
- **Kliensoldal (JavaScript + Cropper.js):** Amikor a felhasználó tallóz egy fájlt, a fájl még nem megy el a szerverre. Betöltődik a böngésző memóriájába egy FileReader segítségével. A felhasználó kivágja a kívánt (pl. 1:1) területet. A gombnyomásra a JavaScript legenerál egy veszteségesen tömörített, csökkentett felbontású Base64 formátumú stringet a képből.
- **Szerveroldal (Laravel + Intervention Image v3):** A kontroller megkapja ezt a hosszú stringet (pl. `data:image/jpeg;base64,/9j/4AAQSk...`). A stringet az algoritmus szétdarabolja: leválasztja a metaadatokat (hogy kiderítse, eredetileg png vagy jpg volt), dekódolja a maradék karakterláncot bináris képadattá (`base64_decode`), majd az Intervention Image könyvtár segítségével memóriába tölti. Végül egy biztonságos, egyedi, véletlenszerű fájlnévvel (`Str::uuid()`) `.webp` kiterjesztésben elmenti a publikus fájlrendszerbe. A régi képfájlt a szerver egy `Storage::delete()` paranccsal automatikusan letörli, megelőzve a szemetelődést. Ezzel az algoritmussal egy 10 MB-os képből gyakran 50-100 Kilobyte-os, webre optimalizált fájlt csinálunk minőségromlás nélkül.

**4. Jogosultsági Mátrix és Admin "Bypass" (Policy Interceptors)**
Az alkalmazás komplex jogosultságokkal dolgozik. Például egy "Private Jar"-t csak a létrehozója láthat. Egy "Friends Only Jar"-t a létrehozó és annak barátai. Ennek az ellenőrzése minden lekérdezésnél rémálom lenne. Ezt a Laravel Policy (Irányelv) osztályaival oldottuk meg.

Létrehoztunk egy `JarPolicy` osztályt, amiben definiáltuk a `view(User $user, Jar $jar)` metódust. Ez leírja a bonyolult üzleti szabályokat:
1. Ha a Jar "public", térjen vissza True-val.
2. Ha a Jar "private", ellenőrizze, hogy `$user->id == $jar->created_by`.
3. Ha a Jar "friends_only", ellenőrizze az adatbázisban a baráti kapcsolatot.

A legszebb része az algoritmusnak azonban az Admin felülbírálat. Minden Policy rendelkezik egy "mindenek előtt" lefutó metódussal:
```php
public function before(User $user, string $ability): bool|null
{
    if ($user->is_admin) {
        return true; 
    }
    return null; 
}
```
Ha a bejelentkezett felhasználó adminisztrátor, a rendszer azonnal engedélyt ad a műveletre, le sem futtatja a mögöttes, drága SQL ellenőrzéseket. Ha nem admin (null-t ad vissza), a folyamat zökkenőmentesen halad tovább a specifikus szabályok felé. Ez az Elválasztás Elvének (Separation of Concerns) gyönyörű megvalósítása a kódunkban.

**5. Idő-alapú Lejáró Meghívó (TTL - Time To Live) Algoritmus**
*Helyszín: `App\Http\Controllers\JarInvitationController`*
Amikor a Chaten keresztül valaki meghív egy barátot egy Jar-ba, az adatbázisban a `jar_invitations` táblába bekerül egy rekord `'pending'` státusszal. A rendszernek garantálnia kell, hogy ez a meghívó 5 perc után megsemmisül, hogy fenntartsa a "Side Quest" spontán izgalmát. Ezt nem szerveroldali Cron job-bal (ami percenként futna és törölgetne), hanem okos validációval oldottuk meg.

Amikor a meghívott fél rákattint az "Elfogad" (Accept) végpontra, az algoritmus ellenőrzi a lejárati időt:
```php
$timeElapsed = $jarInvitation->updated_at->addMinutes(5);
if ($timeElapsed->isPast()) {
    return response()->json(['error' => 'Invitation has expired.'], 422);
}
```
Ezután a rendszer szinkronizálja a felhasználót a Jar-ral:
`$jarInvitation->jar->users()->syncWithoutDetaching([$jarInvitation->invitee_id => ['streak' => 0]]);`
A `syncWithoutDetaching` egy zseniális beépített algoritmus, amely biztosítja, hogy ha a felhasználó valamilyen hiba folytán kétszer kattintana a gombra, ne kerüljön be kétszer az adatbázisba, és ne is dobjon SQL hibát. Csak akkor szúrja be a rekordot a 0-s kezdő streakkal, ha még tényleg nincs benne.

**6. Egyesített Idővonal Generátor (Timeline Aggregator)**
A felhasználó kezdőlapján (Proof Feed) és a Chat ablakban különböző típusú adatok jelennek meg egyetlen összefüggő folyamként (stream). A Chat esetében például a szöveges üzeneteket (`messages`) és a meghívókat (`jar_invitations`) kell időrendbe állítani. 
Ehhez egy olyan adatbázis lekérdezést írtunk, amely külön lekéri a két táblából az adatokat (kapcsolatokkal együtt, Eager Loading), a szerver memóriájában Collection-ökké (kibővített tömb) alakítja őket, majd a két kollekciót összefésüli (`merge()`), és végül ráfuttat egy `sortByDesc('created_at')` rendezési algoritmust. Bár ez memóriaintenzív lehet nagyon nagy adathalmaznál, a pagination (oldalszámozás) bevezetésével (egyszerre csak 50 elemet tölve) a komplexitás O(N log N) marad, és a felhasználó számára egy lineáris beszélgetési idővonalat eredményez.

### 2.4 Tesztdokumentáció

Szoftverünk minőségbiztosítási sarokkövét a széleskörű, automatizált és manuális tesztelés adta. A tesztelés célja kettős volt: egyrészt biztosítani a "Kiváló" minősítéshez elvárt üzembiztosságot, másrészt a refaktorálások (kód-optimalizálások) során azonnal észlelni az esetleges mellékhatásokat (regressziós tesztelés).

**Tesztelési környezet és stratégia**
A projekt automatizált tesztjeit a PHP Unit 11 keretrendszerrel írtuk. Ezt kiegészítettük a Laravel HTTP tesztelő osztályaival, amelyek képesek API hívásokat szimulálni, formákat beküldeni, és DOM elemek meglétét vizsgálni (HTML string validáció).
- *Sebesség optimalizálás:* Az éles adatbázis helyett a PHPUnit környezetet egy memóriában futó (`in-memory`) SQLite adatbázisra konfiguráltuk a `phpunit.xml` fájlban. Minden egyes teszt előtt a `RefreshDatabase` trait (jellemző) lefut, felépíti az adatbázis sémát, a teszt végén pedig megsemmisíti azt. Így a tesztek teljesen izoláltak, nem okoznak "side-effect"-et egymásnak.
- *Lefedettség:* 39 dedikált Feature Test (funkcionális teszt) fut végig a szoftver teljes életciklusán, a regisztrációtól a posztolásig. A teljes tesztsor lefutási ideje kevesebb, mint 3 másodperc a fejlesztői gépeken.

**Tesztesetek részletes bemutatása (Happy Path & Sad Path)**

A tesztelési filozófiánk alapja az úgynevezett "Happy Path" (amikor a felhasználó mindent jól csinál és a rendszer engedi) és a "Sad Path" (amikor a felhasználó hibázik, rosszindulatú, és a rendszernek ezt elegánsan kezelnie kell) szcenáriók lefedése volt. Ezt a hármas felosztás (Arrange - Act - Assert) mintájára írtuk meg.

*1. Autentikáció és Regisztráció Tesztelése (`AuthTest`)*
- **Happy Path:** Létrehozunk a tesztben egy érvényes adatcsomagot (username: TestUser, valid email, jelszó: `Strong!Pass123`, birth_date: 2000-01-01). Elküldjük a POST kérést a `/register` végpontra. Az *Assert* fázisban ellenőrizzük, hogy a válasz státuszkódja sikeres átirányítás-e (`assertRedirect('/home')`), és hogy az adatbázisban megjelent-e a felhasználó (`assertDatabaseHas('users', ['email' => ...])`).
- **Sad Path (Validációs teszt):** Ugyanezt a tesztet lefuttatjuk úgy, hogy a jelszó csak `password` (túl egyszerű), a születési dátum pedig a tegnapi nap (nem éri el a 16 évet). Az *Assert* fázisban azt várjuk, hogy a státuszkód HTTP 302 legyen, visszairányítson a regisztrációs oldalra, és a Session-ben ott legyenek a megfelelő angol nyelvű hibaüzenetek (Error bags) a `password` és `birth_date` mezőkre hivatkozva. A teszt garantálja, hogy a biztonsági szabályok megkerülhetetlenek.

*2. Jogosultságkezelés és Policy Tesztek (`JarAccessTest`)*
Ezek a legkritikusabb tesztek az adatbiztonság szempontjából.
- Létrehozunk egy Felhasználó A-t (User A) és egy Felhasználó B-t (User B). User A létrehoz egy privát Jar-t (`type => 'private_jar'`).
- A tesztprogram bejelentkezik User B-ként, és megpróbálja közvetlenül megnyitni az URL-t (pl. `GET /jars/1`). 
- A várt eredmény egy `assertStatus(403)` HTTP Forbidden hiba.
- Azután a tesztprogram átvált a beépített Admin felhasználóra (`TBZadmin`), és ugyanazt a privát Jar-t próbálja megnyitni. Ekkor az elvárt státusz HTTP 200 (OK), bizonyítva, hogy a leírt Admin Bypass algoritmus hibátlanul működik.

*3. Jar Csatlakozás és Meghívó (Invitation) Flow Tesztelése*
Egy komplex interakciót szimuláló teszt.
- *Lépés 1:* User A létrehoz egy 'invite_only_jar'-t.
- *Lépés 2:* User A meghívót küld User B-nek a chat végponton keresztül. A teszt ellenőrzi a `jar_invitations` táblát (`assertDatabaseCount('jar_invitations', 1)`).
- *Lépés 3:* A teszt átállítja az idősíkot (a Laravel `Carbon::setTestNow()` metódusával) pontosan 6 perccel későbbre.
- *Lépés 4:* User B megpróbálja elfogadni a meghívót. A teszt elvárja, hogy a válasz 422 (Unprocessable Entity) legyen, és a `jar_users` táblába ne kerüljön be User B, mert a meghívó lejárt.

*4. Fájlfeltöltés szimulációja (Storage Fake)*
Annak érdekében, hogy a tesztek során a fejlesztő gép ne teljen meg szemétképekkel, a Laravel `Storage::fake('public')` funkcióját használtuk.
- A teszt generál egy 5 MB-os ál-képfájlt (`UploadedFile::fake()->image('test.jpg')->size(5000)`).
- Beküldi a Jar létrehozó űrlapon. Mivel a validáció csak 4 MB-ig (4096 kb) engedi, a teszt elvárja a fájlméret validációs hibát.
- Másik ágon egy 1 MB-os képet küld be base64 stringként szimulálva a croppert. A teszt a végén megnézi, hogy a mű-tárhelyen (Fake disk) létrejött-e a megfelelő `.webp` fájl, majd letörli azt.

**Manuális és Reszponzivitás Tesztelés**
Az automatizált kód nem látja a dizájnt. Ezért elvégeztük a UI (User Interface) és UX (User Experience) manuális tesztelését a Google Chrome DevTools eszközével.
- Ellenőriztük az oldalt asztali nézetben (1920x1080), Tablet nézetben (pl. iPad Air 820x1180), és szigorú mobil nézetekben (pl. iPhone SE 375x667). 
- Fő fókusz: Kiszűrtük a vízszintes görgetősávokat (overflow-x hidden), ellenőriztük a Livewire "Loading" state-eket (gombok letiltása amíg a kérés fut, nehogy duplán küldjenek el adatot), és megbizonyosodtunk róla, hogy a sötét téma kontrasztaránya megfelel az alapvető olvashatósági követelményeknek (WCAG).

**Tesztelői hozzáférés a Vizsgabizottság számára:**
Bárki, aki ki szeretné próbálni a rendszert élőben, az előre feltöltött (Seed-elt) adatbázissal az alábbi teljes körű adminisztrátori fiókkal tud bejelentkezni. Ezzel a fiókkal kipróbálható a barátfelvétel (másik előre generált userekkel, pl. `testuser`), a postok törlése és a "Official Jar" létrehozás is.
- **Email:** `TBZadmin@gmail.com`
- **Jelszó:** `//bitproject2626\\`

### 2.5 Fejlesztési lehetőségek

A SideQuestJar jelenlegi architekturális formájában egy tökéletesen működő, skálázható MVP (Minimum Viable Product). Az alaptechnológiák (REST API, JSON válaszok) azonban úgy lettek kiválasztva, hogy az alkalmazás a vizsga után akár üzleti projektként is folytatható legyen. Az alábbiakban felvázoljuk a lehetséges fejlesztési irányvonalakat, a rövid távútól a komplex integrációkig.

1. **Natív Mobilalkalmazás fejlesztése (Cross-platform)**
    - *Technikai háttér:* A `routes/api.php` fájlban már definiálva vannak a mobil klienseket kiszolgáló végpontok, amelyek Laravel Sanctum tokenekkel kommunikálnak. 
    - *Megvalósítás:* Flutter, React Native, vagy a Microsoft-féle .NET MAUI keretrendszerrel egy olyan alkalmazás építhető, amely mind iOS, mind Android eszközökön natívan fut. A mobilapp képes lenne a telefon hardverét (kamera közvetlen használata, GPS adatok helyhez kötött Side Quest-ekhez) is kihasználni, ami drasztikusan növelné a felhasználói élményt.
2. **Push Értesítések (Push Notifications) és WebSocket integráció**
    - Jelenleg az értesítések (új meghívó, új chat üzenet) AJAX alapú "polling" (időközönkénti lekérdezés) segítségével frissülnek a weben. Ezt a jövőben a **Laravel Reverb** vagy **Pusher** szolgáltatásra cserélve valódi WebSocket (TCP kapcsolat) alapú valós idejűséget érnénk el, csökkentve a szerver HTTP kéréseinek számát. Mobilon pedig a Firebase Cloud Messaging (FCM) integrációjával a zárolt képernyőre is küldhetnénk azonnali értesítéseket ("Új napi feladat vár!").
3. **Pénzkeresési modell (Monetizáció) és Integrált Merch Store**
    - Egy valós projektnek bevételeket kell termelnie a fenntartáshoz. A rendszerbe építhető egy "Pro" előfizetéses modell (Stripe API integrációval), amely korlátlan privát Jar létrehozását tenné lehetővé, exkluzív profilkereteket adna. Továbbá a felgyülemlett XP-t a jövőben egy belső webshopban kuponokra, vagy valós, márkázott ajándéktárgyakra (fizikai "SideQuestJar" üvegekre) lehetne beváltani.
4. **AI (Mesterséges Intelligencia) Alapú Tartalomgenerálás**
    - Az OpenAI (ChatGPT) API integrálása óriási lehetőséget rejt. A felhasználó kérhetné az AI-t, hogy hozzon létre számára egy "30 napos Fitness Kezdő Jar"-t. Az AI másodpercek alatt generálna 30 logikusan felépített, egyre nehezedő feladatot (címmel, leírással) az adatbázisba, DALL-E segítségével pedig megfelelő ikonokat generálna hozzá. Ezzel a tartalomgyártás kimeríthetetlenné válna.
5. **Kiterjesztett Gamifikáció és Statisztikai Irányítópult (Dashboard)**
    - Bár az XP és a Streak rendszer stabil, a motiváció fenntartásához érdemes beépíteni egy "Kitűző" (Achievement/Badge) rendszert (pl. "Éjszakai Bagoly" kitűző annak, aki éjfél után teljesít, vagy "Maratonista" 100 napos sorozat után). A profil oldalon Chart.js segítségével látványos diagramokat lehetne megjeleníteni a felhasználó havi aktivitásáról és kategóriánkénti (sport, művészet, kreatív) teljesítményéről.

---

## 3. Felhasználói dokumentáció

Ez a fejezet lépésről lépésre haladva végigvezeti az alkalmazás végfelhasználóját (Legyen az átlagember vagy Adminisztrátor) a szoftver telepítési és használati folyamatain. Olyan megközelítést alkalmaz, amely laikusok számára is teljesen érthető.

### 3.1 A program célja és lényegesebb funkciói

Kedves leendő Kalandor! Üdvözlünk a **SideQuestJar** digitális világában! 

A SideQuestJar egy különleges, számítógépen és telefonon egyaránt használható felület, amelynek legfőbb célja, hogy játékot csempésszen a szürke hétköznapokba. Képzeld el úgy az oldalt, mint egy virtuális polcot, tele különböző varázsüvegekkel, "Jar"-okkal. Ezek az üvegek tele vannak izgalmas, érdekes vagy éppen hasznos apró kihívásokkal, feladatokkal – úgynevezett "Quest"-ekkel. A te feladatod egyszerű: lépj be minden nap, nyiss ki egy üveget, nézd meg milyen véletlenszerű kihívást sorsolt neked aznapra a gép, és teljesítsd azt! Lehet ez egy séta, egy kedves üzenet írása egy rég látott ismerősnek, vagy egy kis takarítás. Ha sikerült, fotózd le, zsebeld be a pontokat, építsd a napi sorozatodat (Streak), és figyeld, ahogy a barátaid is csatlakoznak a kalandhoz.

**Mit nyújt számodra az alkalmazás? Főbb funkciók:**
- **Böngészés kötelezettségek nélkül:** Látogatóként is körülnézhetsz. Megnézheted, mik a legnépszerűbb, legtöbb like-ot kapott Jar-ok.
- **Saját kalandok indítása (Regisztrált funkció):** Bármikor létrehozhatsz saját Jar-t. Lehet ez egy teljesen Titkos (Private) Jar, amit csak te látsz, vagy Publikus, amihez bárki a világon csatlakozhat.
- **Közösségi Üzenőfal (Proof Feed):** Megoszthatod másokkal a sikereidet egy fotóval kísérve, és motiválhatjátok egymást like-okkal (szívecskékkel).
- **Beépített Valós Idejű Chat:** Nem kell harmadik féltől származó alkalmazás (pl. Messenger) a kommunikációhoz. Cseveghetsz a barátaiddal, és ide küldhetitek be az exkluzív meghívókat a titkos kihívásokhoz.

### 3.2 Szükséges hardver- és szoftvereszközök

Kiemelt figyelmet fordítottunk arra, hogy a SideQuestJar a lehető legelérhetőbb legyen, így nem kell hozzá sem erős gamer PC, sem legújabb iPhone, és még csak letöltened sem kell semmilyen furcsa programot a gépedre.

- **Hardver követelmények:** Bármilyen digitális eszköz – asztali számítógép (PC/Mac), munkahelyi laptop, tablet (iPad, Galaxy Tab) vagy egy modern okostelefon (Android vagy iOS) –, ami rendelkezik egy használható méretű kijelzővel és interneteléréssel.
- **Szoftver követelmények:** Kizárólag egy modern **Webböngésző** szükséges az eszközödre telepítve. Javasoljuk a Google Chrome, Mozilla Firefox, Microsoft Edge (PC-n) vagy a Safari (Apple eszközökön) alkalmazások legfrissebb verzióit a maximális biztonság és a tökéletes megjelenés érdekében. A régebbi (pl. Internet Explorer 11) böngészők nem támogatottak, azokon az oldal hibásan jelenhet meg.
- **Internetkapcsolat:** Folyamatos, szélessávú internetkapcsolatra van szükség a valós idejű chat működéséhez és a fényképek zökkenőmentes feltöltéséhez a rendszerünkbe.

### 3.3 Telepítés és indítás lépései

A szoftver felhőalapú ("Cloud-native") szolgáltatásként üzemel, így a klasszikus értelemben vett telepítés felesleges. Az indítás lépései a következők:

1. Keresd meg a használt eszközödön a Kedvenc webböngésződet, és nyisd meg.
2. Kattints a felül található URL címsorba, és gépeld be az alkalmazás webcímét. *(Jelen dokumentáció írásakor, a fejlesztési stádiumban ez egy lokális cím vagy tesztszerver címe. Az élesítés után ez valószínűleg a `https://sidequestjar.com` formát ölti).* Nyomj entert.
3. A szerverünk pár tizedmásodperc alatt letölti az eszközödre az oldalt, és azonnal a vizuálisan gazdag, sötét tónusú **Kezdőképernyőn** (Home) találod magad.
4. **Tipp mobiltelefont használóknak (PWA funkció):** Ha Androidot (Chrome böngészőt) vagy iPhone-t (Safarit) használsz, a böngésző beállításaiban válaszd a *"Hozzáadás a kezdőképernyőhöz"* (Add to Home Screen) opciót. Ekkor megjelenik egy ikon a telefonod menüjében, és rábökve a SideQuestJar úgy fog elindulni és viselkedni, mint egy App Store-ból letöltött teljes képernyős natív alkalmazás!

![Kép helye: A program kezdőképernyője asztali és mobil nézetben egymás mellett]()

### 3.4 A program részletes bemutatása

Hogyan használd a rendszert a mindennapokban? Vezessük végig egy tipikus felhasználó útját!

#### 1. A Kezdőlap (Home) felfedezése
Az első találkozás az oldallal bejelentkezés nélkül is látványos. A lap tetején lévő **Navigációs Sávban** találod a projekt logóját (balra), illetve a "Login" és "Register" gombokat (jobbra). Ha telefonról nézed, ezek egy "Hamburger" menü mögé (három kis vonal) vannak elrejtve, a helytakarékosság miatt.

Középen egy gigantikus kártyát látsz: ez a **"Today's Main Quest" (A Nap Fő Küldetése)**. Ez a mi "Napi ajánlatunk", egy olyan feladat, amit minden látogató lát. Kiváló motiváció lehet, ha épp nincs ötleted, mit csinálj a szabadidődben.
Ha lejjebb görgetsz, elegáns, oldalra csúsztatható listákat (Carousel) fogsz találni:
- **Proof Feed (Bizonyíték fal):** Itt láthatod fotókkal dokumentálva azokat a feladatokat, amiket más bátor kalandorok a közelmúltban sikeresen teljesítettek az oldalon. Ezt végignézve azonnal megérted az alkalmazás célját.
- **Most Popular Jars (Legnépszerűbbek):** A legtöbb aktív tagot számláló kihívás-üvegek.
- **Most Liked Jars (Legkedveltebbek):** A felhasználók szavazatai (Szívecskéi) alapján rangsorolt gyűjtemények.

#### 2. Regisztráció és Belépés (A kaland kezdete)
Ahhoz, hogy pontokat és sorozatot (Streak) tudj gyűjteni, csatlakoznod kell a platformhoz. Kattints a jobb felső sarokban a **"Register"** gombra!

Egy stílusos űrlapot fogsz látni. Itt a következőket kell megadnod:
- **Username (Felhasználónév):** Légy kreatív! Ez fog megjelenni a posztjaid felett. Egyedinek kell lennie a rendszerben (pl. *Sárkányölő99*).
- **Birth Date (Születési dátum):** Az alkalmazás szigorú adatvédelmi szabályai (GDPR) miatt csak 16 éven felüliek regisztrálhatnak. A naptár ikonra kattintva könnyedén kiválaszthatod a dátumot.
- **Full Name (Teljes Név):** A hivatalos neved.
- **Email Address (Email):** Valós email címet adj meg, mert elfelejtett jelszó esetén csak ide tudunk jelszó-visszaállító linket küldeni.
- **Password (Jelszó):** *Nagyon fontos lépés!* A fiókod védelme érdekében a rendszerünk csak úgynevezett "Erős jelszavakat" fogad el. Szabályok: minimum 12 karakter hossz, kell bele legalább egy Nagybetű, egy kisbetű, egy számjegy és egy speciális írásjel (például kérdőjel, felkiáltójel vagy kukac: ?, !, @). Ha ezt elrontod, a rendszer jelezni fog! Írd be újra a biztonság kedvéért a Confirm Password mezőbe is.
- Kattints a nagy kék "Register" gombra, és bent is vagy!

A jövőben elég lesz csak a **"Login"** (Bejelentkezés) gombot használnod az email címeddel és a jelszavaddal.

![Kép helye: A regisztrációs űrlap, kitöltve]()

#### 3. Csatlakozás a kihívásokhoz (Jar-okhoz)
Bejelentkezés után a kezdőlap megváltozik. Fent a menüsorban megjelenik a profilképed, és egy láng ikon egy számmal: ez a te **Heti Sorozatod (Streak)**. A cél, hogy ez a láng ne aludjon ki, azaz lehetőleg minden nap jelentkezz be, és csinálj meg legalább egy feladatot. Megjelenik egy új Carousel lista is a kezdőlapon: a "My Jars", ami a te saját gyűjteményeidet listázza.

Kattints rá egy szimpatikus Jar-ra (például a "Legnépszerűbbek" közül). Ekkor bekerülsz a Jar saját oldalára. Itt olvashatod el a részletes leírást. Ha tetszik, kattints az oldal tetején a hatalmas, színes **"Join Jar" (Csatlakozás)** gombra. Amint ezt megteszed, hivatalosan is taggá válsz, bekerülsz az oldalon található "Leaderboard" (Ranglista) táblázatba a többiek mellé, nulla kiinduló streakkal. Bármikor kiléphetsz a "Leave Jar" gombbal.

#### 4. A Napi Quest teljesítése és posztolása (Proof)
Minden Jar-ban, aminek tagja vagy, minden egyes nap megjelenik egy új Napi Küldetés (Daily Quest) kártya a csatlakozás gomb alatt. Ez a feladat 24 óránként frissül, így érdemes naponta visszanézni!

1. Kattints a napi feladat kártyáján lévő **"Complete" (Teljesítem)** gombra.
2. Felugrik egy ablak, ahol igazolhatod a teljesítést egy fájlfeltöltő mezőben (**Upload Proof**). Fotózd le a telefonoddal az elkészült ételt, a rajzot, vagy épp a konditermet, ahol voltál, és töltsd fel a képet.
3. Beállíthatod, ki lássa ezt a sikert: mindenki (Public), csak a barátaid, vagy csak Te (Private).
4. Nyomd meg a küldés gombot! A rendszer jutalmaz egy remek animációval, a láng ikonod (Streak) számlálója egyet ugrik felfelé, a profilodhoz pedig hozzáadódnak a jól megérdemelt XP pontok.

#### 5. Saját Jar létrehozása
A felső menüsorban lévő egyértelmű **"+" ikonra**, vagy a profilodon a Create Jar gombra kattintva válhatsz te is kalandmesterré.
- Tölts ki minden mezőt: Adj egy beszédes nevet, írj leírást.
- **Képfeltöltés Vágóval:** Tölts fel egy borítóképet a Jar-odnak (Maximum 4MB, JPG/PNG). Amikor feltöltöd, az oldalon egy professzionális, egérrel és ujjakkal is húzogatható vágóeszköz jelenik meg. Állítsd be pontosan, melyik részlete látszódjon a fotónak, majd hagyd jóvá! A szerverünk gondoskodik róla, hogy ez tökéletes felbontásban mentsen el.
- **Láthatóság (Type) beállítása:** Itt dől el a sorsa a doboznak. 
    - *Public:* Bárki láthatja és csatlakozhat.
    - *Private:* Csak a te szemednek. Tökéletes személyes szokáskövetéshez.
    - *Friends Only:* Ha csatlakozni akarnak, előbb be kell jelölniük téged barátnak.
    - *Invite Only:* Top secret. Csak az léphet be, akinek te személyesen küldesz egy linket/meghívót a chaten.

Miután létrehoztad, a Jar oldalán a "Quests" szekcióban kezdhetsz el új feladatokat (Create Quest) hozzáadni a dobozodhoz, hasonlóan egyszerű űrlap segítségével.

![Kép helye: Profil oldal és a Jar létrehozó képvágó modul bemutatása]()

#### 6. Szociális élet: Barátok felvétele és a Chat
Az élmény sokkal jobb társaságban! A profilképedre kattintva a lenyíló menüben válaszd a **"Friends"** opciót.
- **Barátkereső:** A keresősávba elég elkezdened gépelni az ismerősöd felhasználónevét. Rendszerünk azonnal felkínálja a találatokat alatta lévő listában. Kattints a nevére, majd a "Add" gombra, hogy elküldd a kérést.
- Ugyanezen az oldalon tudod elfogadni (zöld pipa) azokat a kéréseket, amiket te kaptál másoktól.
- Ha már vannak barátaid, a menüből a **"Chat"** menüpont is érdekes lesz. Ez egy valós idejű levelező felület. Bal oldalt látod a barátaidat, jobb oldalt (vagy mobilon a névre koppintva) pedig egy modern, azonnali üzenetküldő ablakot, pont mint a közösségi médiában. Ide gépelhetsz nekik.
- **A varázslat: Meghívó küldése.** A chat ablakban lévő "Invite to Jar" gombbal átdobhatsz a barátodnak egy digitális meghívót bármelyik saját készítésű Jar-odba. Egy szép kártya fog megjelenni a chatben neki. De vigyázat! Ez egy küldetés: a meghívó az elküldéstől számított **5 perc múlva megsemmisül**! A barátodnak gyorsan az "Elfogad" gombra kell kattintania, különben lemarad a buliról.

#### 7. Adminisztrátorok különleges képességei
(Ez a rész azoknak szól, akik rendszergazdai, azaz Admin fiókkal rendelkeznek az adatbázisban, ilyen a tesztelői fiók is).
Az adminok életben tartják a rendet és a minőséget. Ha egy Admin belép, a profil lenyíló menüjében talál egy új, **"Admin Panel"** gombot.
- Ezen a műszerfalon egy táblázatos felületen kilistázva látja a teljes regisztrált felhasználói bázist.
- Gombnyomással adhat Admin jogokat megbízható usereknek (Toggle Admin), vagy törölhet rendbontókat (Delete User).
- Bárhol az oldalon jár, az Admin képes törölni vagy szerkeszteni bármilyen, mások által írt Jar-t vagy Questet, ha azok sértik a felhasználási feltételeket.
- Létrehozhat úgynevezett **"Official Jar"**-t, ami a keresőkben és a listákban kiemelt helyen és plecsnivel jelenik meg (pl. "Hivatalos SideQuest Tavaszi Kihívás").

### 3.5 Hibajelzések és helytelen használat

Senki sem szereti a lefagyó weblapokat vagy az ijesztő, angol nyelvű hibaüzenetekkel teleírt képernyőket (úgynevezett Exception oldalakat). A SideQuestJar fejlesztésekor hatalmas energiát fektettünk abba, hogy ha véletlenül rossz gombra kattintasz, vagy elrontasz valamit az űrlapokon, a program udvariasan, a dizájnba illeszkedve tájékoztasson a megoldásról.

*Tipikus szituációk, amiket a rendszer kivéd (Sad Paths):*

- **"Elrontottam a jelszót belépéskor"**: Semmi gond. A felület nem fog összeomlani. A login űrlap felett, vagy a jelszó mező alatt egy diszkrét, piros színű hibaüzenet (pl. *"These credentials do not match our records."*) figyelmeztet, hogy próbáld újra, a jelszavad pedig biztonsági okokból automatikusan kitörlődik a mezőből.
- **"Elfelejtettem nevet adni az új dobozomnak"**: Ha egy Jar készítésekor üresen hagyod a Név (Name) mezőt, és rányomsz a mentésre, a weblap nem küldi el az adatokat. Helyette a hibás mező piros keretet kap, és alatta megjelenik a figyelmeztetés: *"The name field is required."* Ugyanez történik, ha egy képet akarsz feltölteni, de az nagyobb, mint a megengedett 4 Megabyte.
- **"Bemásoltam a böngészőbe egy titkos linket"**: Tegyük fel, hogy valaki elküldi neked SMS-ben egy titkos, Private Jar közvetlen linkjét, amit ő hozott létre. Ha be vagy jelentkezve, és rákattintasz, a szerverünk ellenőrzi a jogosultságodat (lásd: fejlesztői dokumentáció / Policy rendszer). Mivel nem te vagy a tulajdonos, ahelyett, hogy betöltődne a titkos tartalom, egy elegáns "403 Forbidden - Hozzáférés megtagadva" értesítést kapsz a képernyő közepére. A rendszer atombiztos.
- **"Spammelni akarom a barátkérést"**: Elkezded ötször egymás után lenyomni a "Barát Hozzáadása" gombot ugyanannál a felhasználónál, vagy beírod a saját neved. A képernyő tetején (egy úgynevezett Toast, vagy Flash üzenetben) egy pillanatra felvillan egy hibaüzenet: *"Friend request already exists."* (A kérés már létezik) vagy *"You cannot add yourself as a friend"* (Magadat nem veheted fel barátnak). Az adatbázis tiszta marad.
- **"Túl későn kattintottam a Chat meghívóra"**: A barátod küld egy Jar meghívót a chat ablakba. Elutazol, és csak két óra múlva látod meg az üzenetet. A gomb még ott van. Rákattintasz az "Elfogadás" gombra. A gomb helyett egy piros üzenet jelenik meg az arcodban: *"Invitation has expired."* (A meghívó lejárt). Mivel a limit 5 perc, arra kell kérned a barátod, hogy generáljon neked egy újat!

![Kép helye: Tipikus validációs hibát (túl rövid jelszó, üres mező) jelző űrlap screenshotja]()

### 3.6 Információkérés lehetőségei

Hiszünk benne, hogy a SideQuestJar felülete magáért beszél, letisztult, és a modern ikonok (szív, plusz jel, chat buborék) mindenki számára egyértelmű navigációt biztosítanak. Amennyiben azonban technikai malőrbe ütköznél (például nem jelenik meg egy kép, amit feltöltöttél), esetleg kizártad magad a fiókodból, vagy csupán van egy fantasztikus ötleted egy új funkcióra (pl. "Mi lenne ha lehetnének videós Questek is?"), a csapatunk nyitott az értékes visszajelzésekre!

- **Hivatalos Támogatási Email:** Írj egy részletes elektronikus levelet a dedikált fejlesztői címünkre: **support@sidequestjar.com**. Arra kérünk, hiba bejelentése esetén írd le pontosan, melyik eszközről (pl. iPhone 13, Safari), és milyen művelet közben tapasztaltad a problémát. A megkeresésekre a csapat általában munkanapokon, 24-48 órán belül válaszol.
- **Gyors Kapcsolatfelvétel (Contact form):** Nem szeretnél belépni a Gmail-be? Semmi gond! Görgesd le bármelyik oldalt az aljára (a sötét láblécbe, Footer-be). Itt találsz egy "Contact Us" (Kapcsolat) hivatkozást. Erre kattintva megnyílik egy egyszerű űrlap, ahonnan az üzeneted azonnal, egyenesen a projekt belső hibajegy (ticket) rendszerébe érkezik.

---

## 4. Összefoglalás, köszönetnyilvánítás

A SideQuestJar projekt fejlesztése több hónapos, intenzív, de hihetetlenül motiváló utazás volt csapatunk (Karácsony Zoltán, Borza Gergő, Nagy Panna Viktória) számára. Visszatekintve erre az időszakra, büszkén jelenthetjük ki, hogy a kezdeti, merésznek tűnő szakmai célkitűzéseket sikerült maradéktalanul teljesítenünk. Egy egyszerű, egyhangú "To-Do" feladatkezelő leprogramozása helyett egy olyan modern, összetett, közösségi interakciókra épülő szoftver platformot tettünk le az asztalra, amely technológiai hátterében (REST API, Livewire reaktivitás, robusztus Policy-k, komplex adatbázis logikák) megállja a helyét a vállalati piacon is.

A legbüszkébbek arra a technikai áttörésre vagyunk, amit a felhasználói élményben (UX) és az aszinkron adatkezelésben (AJAX) elértünk: a chat funkció és a képvágó modul integrálása a böngészőbe prémium érzetet ad a weboldalnak, elmosva a határt egy weblap és egy letöltött telefonos applikáció között. Természetesen a tanulási folyamat nem volt zökkenőmentes. A legnagyobb kihívást a polimorfikus adatbázis-kapcsolatok tervezése, a Livewire komponensek állapotkezeléséből (State management) fakadó "Snapshot Missing" hibák felderítése, valamint a reszponzív, mobilon sem elcsúszó design pixel-tökéletes kialakítása okozta. A csoportmunkát a Git verziókövető rendszeren keresztül oldottuk meg, amely során megtanultuk a kódkonfliktusok (merge conflicts) türelmes feloldását, és felismertük a folyamatos, tiszta kommunikáció (Clean Code és jó kommentek) erejét a csapaton belül.

A projektünk egyáltalán nem ért véget ezzel a dokumentációval. Ez a szoftver egy MVP, aminek szerveroldala már most fel van készítve arra, hogy a jövőben egy natív .NET MAUI keretrendszerben írt, App Store-ból letölthető mobilalkalmazást szolgáljon ki. Reményeink szerint a közeljövőben béta tesztelőket is bevonhatunk a platform élesítéséhez.

**Záró gondolatok és köszönetnyilvánítás:**
Szeretnénk kifejezni őszinte hálánkat a projekt konzulensünknek és az oktatói karnak. A tőlük kapott szigorú, de építő jellegű kritikák, a szoftver-architekturális tanácsok voltak azok, amelyek átsegítettek minket a fejlesztés legmélyebb holtpontjain, és megakadályozták, hogy rossz tervezési (Spaghetti code) irányt vegyünk. 

Hatalmas, személyes köszönet illeti a családtagjainkat, a párunkat és a szűk baráti körünket. Hálásak vagyunk a türelmükért, amit a késő éjszakákba nyúló "már csak ezt a bugot kell kijavítanom, és befejeztem" típusú ígéreteink kapcsán tanúsítottak, és azért is, hogy elnézték a fejlesztési időszak alatt az állandó számítógép előtti görnyedésünket. Végül köszönet jár egymásnak, a Csapatnak: a konstruktív viták, a humor és az egymás kódjának kitartó tesztelése és javítása nélkül ez az alkalmazás sosem öltött volna formát. Köszönjük az utazást, és várjuk a következő Side Quest-et!

---

## 5. Irodalomjegyzék

A rendszer felépítése, tervezése és a felmerülő bugok javítása elképzelhetetlen lett volna a globális nyílt forráskódú (Open-Source) közösség tudásbázisa, valamint a modern szakirodalmak használata nélkül. Az alábbiakban felsoroljuk azokat a kulcsfontosságú forrásokat, dokumentációkat, amelyek a munka gerincét adták:

*Szakmai könyvek és publikációk:*
- Matt Stauffer (2019): *Laravel: Up & Running: A Framework for Building Modern PHP Apps* (2nd Edition). O'Reilly Media. (Az ORM és a routolás alapjaihoz).
- Robert C. Martin (2008): *Clean Code: A Handbook of Agile Software Craftsmanship*. Prentice Hall. (A kódolási standardokhoz és a karbantarthatósághoz).

*Hivatalos keretrendszer dokumentációk és fejlesztői weboldalak:*
- **A Laravel PHP keretrendszer hivatalos, mélyreható dokumentációja (Kifejezetten az Eloquent ORM, Sanctum hitelesítés és a Policies rendszerek):**
  http://laravel.com/docs/12.x/
- **Bootstrap 5 UI keretrendszer dokumentáció (Reszponzív rácsrendszer és előregyártott flexbox komponensek használatához):**
  http://getbootstrap.com/docs/5.2/getting-started/introduction/
- **Livewire hivatalos útmutató (Full-stack reaktív komponensek építéséhez SPA élmény eléréséhez, Javascript írása nélkül):**
  http://livewire.laravel.com/docs/quickstart
- **Cropper.js nyílt forráskódú könyvtár (A kliensoldali, interaktív képvágó és base64 generáló eszköz megvalósításához):**
  http://fengyuanchen.github.io/cropperjs/
- **Adatbázis tervezési és vizualizációs eszköz (Az ER diagramok és folyamatábrák elkészítéséhez):**
  http://app.diagrams.net/
- **Sass (SCSS) pre-processzor útmutató (A változókon alapuló "Dark mode" sötét téma és a stíluslapok strukturálásához):**
  http://sass-lang.com/documentation/
- **Intervention Image v3 (A szerveroldali memóriaintenzív képfeldolgozás, .webp formátummá alakítás és tömörítés implementálásához):**
  http://image.intervention.io/v3/introduction/
- **PHPUnit dokumentáció (Az automatizált funkcionális - Feature - tesztek és az in-memory SQLite szimulációk elkészítéséhez):**
  https://phpunit.de/documentation.html

*(A dokumentáció készült a "SideQuestJar" szoftverhez. Fejlesztők: Karácsony Zoltán, Borza Gergő, Nagy Panna Viktória. Készült: 2026. május)*
