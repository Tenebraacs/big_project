# SideQuestJar — Vizsgaremek Védés (Defence Guide)

> **Focus:** Backend (10 pts) · Desktop Frontend (10 pts) · Mobile Frontend (10 pts) · Code Quality (3 pts) · Testing (3 pts) · Overall Software Evaluation (5 pts)

---

## 1. A szoftver átfogó értékelése (5 pont)

### Mi a SideQuestJar?
A SideQuestJar egy gamifikált feladatkezelő webalkalmazás, ahol a felhasználók „Jar"-okat (üvegeket) hoznak létre, amelyek tematikus kihívásokat (quest-eket) tartalmaznak. A felhasználók csatlakozhatnak mások jar-jaihoz, napi kihívásokat teljesíthetnek, streakeket építhetnek, XP-t gyűjthetnek, és közösségi funkciókat használhatnak (barátok, chat, meghívók).

### Életszerűség és hasznosság (Témaválasztás)
- **Valódi probléma (Kiégés ellen):** A motiváció fenntartása és a monoton hétköznapokból való kiszakadás nehéz. Az alkalmazás nem egy szorongást keltő "To-Do" lista, hanem véletlenszerű napi kalandokat (Side Quest-eket) kínál.
- **Célcsoport:** Fiatalok és felnőttek, akik közösségi élményen keresztül szeretnének új rutinokat kialakítani (sport, művészet, kreativitás).
- **Piaci relevancia:** Hasonló alkalmazások (Habitica, Streaks) többnyire a szigorú produktivitásra fókuszálnak. A SideQuestJar a spontaneitásra, a közös „Jar" koncepcióra és a pozitív megerősítésre épít.

### Komplexitás és kidolgozottság
| Terület | Megvalósított funkciók |
|---|---|
| **Felhasználókezelés** | Regisztráció, bejelentkezés, profil szerkesztés, jelszó-visszaállítás, profilkép feltöltés (image cropping), admin jogosultságok |
| **Jar rendszer** | CRUD, 5 típus (public, private, friends-only, invite-only, official), csatlakozás/kilépés, like-olás, kommentelés |
| **Quest rendszer** | CRUD, napi quest rotáció, quest teljesítés képfeltöltéssel, XP rendszer |
| **Közösségi funkciók** | Barát kérések (küldés/elfogadás/elutasítás), valós idejű chat, jar meghívó rendszer (lejárattal) |
| **Gamifikáció** | Streak rendszer, XP gyűjtés, napi kihívások, Proof Feed (teljesítések megjelenítése) |
| **Admin panel** | Felhasználók kezelése, admin jogosultság kezelése, felhasználó törlése |

---

## 2. Szerver oldali komponens — Backend (10 pont)

### Technológiai stack
| Technológia | Verzió | Szerep |
|---|---|---|
| **Laravel** | 12.x | MVC keretrendszer |
| **PHP** | 8.2+ | Szerver oldali nyelv |
| **Laravel Sanctum** | 4.x | API token-alapú autentikáció |
| **Livewire** | 4.x | Reaktív szerver-oldali komponensek |
| **Intervention Image** | 3.x | Képfeldolgozás (crop, resize, webp konverzió) |
| **SQLite / MySQL** | — | Adatbázis |

### RESTful API architektúra

Az alkalmazás **két különálló kontroller réteget** használ:

1. **Web Controllers** (`App\Http\Controllers\*`) — Blade nézetek kiszolgálása az asztali kliensnek
2. **API Controllers** (`App\Http\Controllers\Api\*`) — JSON válaszok a mobil kliensnek

**7 dedikált API kontroller:**

| Kontroller | Fő végpontok | Leírás |
|---|---|---|
| `Api\UserController` | `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/user` | Regisztráció, bejelentkezés Sanctum tokennel, felhasználó lekérdezés |
| `Api\UserProfileController` | `GET /api/user/profile`, `POST /api/user/profile/update`, `POST /api/user/profile/password` | Profil kezelés, profilkép feltöltés, jelszócsere |
| `Api\JarController` | `GET /api/jars`, `POST /api/jars`, `PUT /api/jars/{id}`, `DELETE /api/jars/{id}`, `POST /api/jars/{id}/join`, `POST /api/jars/{id}/leave` | Teljes Jar CRUD + quest kezelés |
| `Api\JarInteractionController` | `GET /api/jars/{id}/comments`, `POST /api/jars/{id}/comments`, `POST /api/quests/{id}/complete`, `GET /api/jars/{id}/completions` | Kommentek, quest teljesítés, proof feed |
| `Api\FriendController` | `GET /api/friends`, `POST /api/friends/add`, `POST /api/friends/{id}/accept`, `POST /api/friends/{id}/decline`, `DELETE /api/friends/{id}` | Teljes barátkezelés |
| `Api\MessageController` | `GET /api/chat/{user}/messages`, `POST /api/chat/{user}/messages` | Chat üzenetek küldése és lekérdezése |
| `Api\AdminController` | `GET /api/admin/users`, `POST /api/admin/users/{id}/toggle-admin`, `DELETE /api/admin/users/{id}` | Admin felhasználókezelés |

### Autentikáció és biztonság

```
Sanctum Token-alapú auth az API-hoz:
  - POST /api/register → token generálás (1 hét lejárat)
  - POST /api/login → device-specifikus token
  - Middleware: auth:sanctum az összes védett végponton
  
Session-alapú auth a web felülethez:
  - Laravel UI Auth scaffolding
  - CSRF védelem minden form-on
```

### Authorizáció — Policy rendszer

**14 Policy osztály** van definiálva az `app/Policies/` mappában:

| Policy | Szabály |
|---|---|
| `JarPolicy` | Csak a létrehozó szerkesztheti/törölheti; admin bypass (`before()` metódus); `invite()` — csak tagok hívhatnak meg |
| `JarQuestPolicy` | Csak a jar tulajdonosa kezelheti a questeket |
| `MessagePolicy` | Csak barátok küldhetnek üzenetet egymásnak |

**Fontos kiemelni:**
```php
// JarPolicy — Admin bypass pattern
public function before(User $user): bool|null
{
    if ($user->is_admin) {
        return true;  // Admin mindent tehet
    }
    return null;  // Normál felhasználó → policy check
}
```

### Adatbázis tervezés (5 pont)

**18 migrációs fájl**, **14 modell**, **11 factory**, **12 seeder**

**Fő táblák és kapcsolatok:**
```
users ──┬── 1:N ──→ jars (created_by)
        ├── N:M ──→ jars (jar_users pivot: streak)
        ├── N:M ──→ users (friends pivot: status)
        ├── 1:N ──→ messages (sender/receiver)
        ├── 1:N ──→ quest_completions
        ├── 1:N ──→ likes (polymorphic)
        └── N:M ──→ customizations (user_customs pivot)

jars ───┬── 1:N ──→ jar_quests
        ├── 1:N ──→ jar_comments
        ├── 1:N ──→ jar_invitations
        └── morphMany → likes (polymorphic)

jar_quests ── 1:N ──→ quest_completions
```

**Haladó funkciók az adatbázisban:**
- **Soft Deletes:** `users`, `jars`, `jar_quests`, `quest_completions` — az adatok nem vesznek el törléskor
- **Polymorphic kapcsolat:** `likes` tábla — bármilyen likeable entitáshoz csatolható
- **Pivot tábla extra mezőkkel:** `jar_users.streak` — streak nyilvántartás
- **Enum típusok:** `jars.type` (5 típus), `quest_completions.type` (3 típus)
- **Napi quest rotáció:** `jars.current_daily_quest_id` + `previous_daily_quest_id` + `current_daily_quest_date`

### Validáció — Form Request osztályok

| Request | Szabályok |
|---|---|
| `StoreJarRequest` | `name`: required, max:50, unique per user; `type`: enum; `picture`: image, max 4MB; admin-only `official_jar` |
| `UpdateJarRequest` | Ugyanaz, de unique rule figyelembe veszi az aktuális jart |
| `StoreJarQuestRequest` | `title`: required, max:60; `description`: max:1500 |

### Observer pattern
- `JarObserver` — automatikusan beállítja a `created_by` mezőt létrehozáskor
- `JarQuestObserver` — quest-specifikus logika
- `UserObserver` — felhasználó-specifikus események

### Kulcs backend logikák és egyedi algoritmusok

**1. Napi quest rotáció algoritmus (Lazy-evaluation):**
```php
protected function getOrCreateDailyQuest(Jar $jar)
{
    // Ha a mai quest már kiválasztva → visszaadja
    // Ha nem → véletlenszerű quest kiválasztás (előző kizárva)
    // → jar rekord frissítése (current/previous/date)
}
```

**2. Quest teljesítés + XP + Streak:**
```php
public function completeQuest(Request $request, JarQuest $quest)
{
    // 1. Dupla teljesítés ellenőrzés (napi limit)
    // 2. Opcionális kép feltöltés
    // 3. QuestCompletion létrehozása
    // 4. Streak növelés (pivot tábla)
    // 5. XP hozzáadás a felhasználóhoz
}
```

**3. Képvágás és Base64 Feldolgozás (Image Pipeline):**
- A kliens (Cropper.js) memóriában vágja a képet, és csak egy tömörített Base64 stringet küld a szervernek.
- A szerver (Intervention Image v3) ezt dekódolja, és helytakarékos `.webp` formátumban menti el, az előző képet pedig letörli a szemetelődés megelőzésére.

**4. Idő-alapú Lejáró Meghívó (TTL Algoritmus):**
- A chatben küldött Jar meghívók 5 perc után lejárnak. 
- A lejáratot az elfogadás pillanatában (`accept` végpont) ellenőrzi az algoritmus, elkerülve a szervert terhelő háttérfolyamatokat (Cron jobs).

**5. Egyesített Idővonal Generátor (Timeline Aggregator):**
- A chat ablakban a szöveges üzeneteket és a meghívókat egyetlen folyammá (stream) fésüljük össze. A memóriában lévő Collection-ök rendezésével O(N log N) komplexitással érjük el a lineáris beszélgetési idővonalat.

---

## 3. Asztali kliens oldali komponens — Desktop Frontend (10 pont)

### Technológiai stack
| Technológia | Szerep |
|---|---|
| **Blade** | Template engine (SSR) |
| **Livewire 4** | Reaktív komponensek AJAX nélkül |
| **Bootstrap 5** | Responsive CSS framework |
| **SCSS (custom)** | `ownapp.scss` — egyedi dark theme |
| **Vite** | Frontend build tool (HMR) |
| **Bootstrap Icons** | Ikonkészlet |
| **Cropper.js** | Képvágó komponens |
| **Flatpickr** | Dátumválasztó |

### Oldaltérkép (Sitemap)

```
/ (home)
├── Napi Main Quest kártya
├── Proof Feed (teljesítések)
├── Jar Carousel-ek (Popular, Liked, My Jars)
│
├── /jars/create ── Jar létrehozás form
├── /jars/{id} ──── Jar részletes nézet
│   ├── Quest lista + teljesítés
│   ├── Leaderboard (streak rangsor)
│   ├── Komment szekció
│   └── Jar kezelés (edit/delete/join/leave)
├── /jars/{id}/edit ── Jar szerkesztés
│
├── /users/profile ── Saját profil
│   ├── Profil szerkesztő (Livewire)
│   ├── My Jars / Joined Jars
│   └── Profilkép feltöltés + crop
├── /users/{username} ── Más felhasználó profilja
│
├── /friends ─── Barátok kezelése
│   ├── Barátlista
│   ├── Bejövő kérések
│   └── Felhasználó keresés
│
├── /chat ────── Chat lista
├── /chat/{user} ── Chat beszélgetés
│   ├── Üzenetküldés
│   └── Jar meghívók (küldés/elfogadás/elutasítás)
│
├── /login ──── Bejelentkezés
├── /register ── Regisztráció
└── /password/reset ── Jelszó visszaállítás
```

### Livewire komponensek (⚡ = Livewire)

| Komponens | Funkció |
|---|---|
| `⚡home-main-quest` | Napi quest megjelenítése, teljesítés gomb, képfeltöltés |
| `⚡jar-carousel` | Horizontálisan görgethető jar kártyák |
| `⚡proof-feed` | Közösségi feed a quest teljesítésekről |
| `⚡show-jar` | Jar részletes nézet: quests, leaderboard, kommentek, like |
| `⚡create-jar` / `⚡edit-jar` | Jar CRUD űrlapok |
| `⚡create-quest` / `⚡edit-quest` | Quest CRUD űrlapok |
| `⚡admin-users` | Admin felhasználókezelő panel |
| `⚡navbar-streak` | Heti streak vizualizáció a navigációs sávban |
| `⚡user-menu` | Felhasználói menü dropdown |
| `⚡profile-header` / `⚡profile-editor` | Profil megjelenítés és szerkesztés |
| `⚡auth.login-modal` | Bejelentkezési modal (vendégeknek) |
| `image-cropper` | Profilkép / jar kép vágó (Cropper.js integráció) |

### UI/UX jellemzők

- **Dark theme:** Egyedi SCSS-sel megvalósított sötét színvilág
- **Responsive navbar:** Desktop és mobil nézetre külön-külön optimalizált navigáció
- **Carousel:** Horizontálisan görgethető jar kártyák touch supporttal
- **Proof Feed:** Instagram-szerű közösségi feed a teljesítésekről
- **Image Cropper:** Cropper.js integrált képvágó komponens
- **Word counter:** Valós idejű szószámláló a leírás mezőknél
- **Streak vizualizáció:** Heti naptár-szerű streak kijelző a navbar-ban
- **Flash üzenetek:** Siker/hiba visszajelzések minden műveletnél

### Navigáció és layout
```html
<!-- Layout struktúra -->
<body class="background">
  <nav>  ← Navbar (streak, user menu, brand)
  <main> ← Tartalom (@yield('content'))
  <footer> ← Lábléc (kontakt, copyright)
</body>
```

---

## 4. Mobil kliens oldali komponens (10 pont)

### Kétfajta mobil stratégia

**A) Web kliens — Responsive design (Bootstrap 5)**
- `<meta name="viewport" content="width=device-width, initial-scale=1">` — mobil-first viewport
- Bootstrap 5 responsive osztályok: `d-md-none`, `d-none d-md-flex`, `col-12 col-md-6`, stb.
- Külön mobil navigáció: kompakt brand logo (`SQJ`), rövidített menü
- Touch-friendly UI: kellően nagy gombok, carousel swipe

**B) RESTful API mobil klienshez**
- Teljes `/api/*` végpont készlet (40+ endpoint) — **kész a .NET MAUI / React Native / Flutter kliens fogyasztására**
- Sanctum token autentikáció — device-specifikus tokenek
- JSON válaszok minden végponton
- Az Admin panel is API-n keresztül elérhető (MAUI kliens)

### Mobil-specifikus frontend megoldások

| Funkció | Megvalósítás |
|---|---|
| **Külön mobil navbar** | `d-md-none` osztály: kompakt SQJ logo + user menu |
| **Responsive carousel** | Touch-enabled horizontális görgetés |
| **Adaptív grid** | `col-12` mobilon → `col-md-6` / `col-md-4` desktopon |
| **Kompakt kártyák** | Kisebb padding, rövidített szövegek mobilon |
| **Touch targets** | Min. 44px érintési célterületek a gombokon |

### API válasz formátumok

Minden API végpont egységes JSON struktúrát ad vissza:

```json
// Sikeres válasz
{ "message": "Jar created successfully.", "jar": { ... } }

// Hiba válasz
{ "message": "You cannot invite others to this jar." }

// Lista válasz (pagináció)
{ "data": [...], "current_page": 1, "last_page": 5, "total": 48 }
```

---

## 5. A kód minősége (3 pont)

### Clean Code elvek betartása

| Elv | Megvalósítás |
|---|---|
| **Single Responsibility** | Kontrollerek egyetlen erőforrást kezelnek; Form Request-ek külön fájlban; Policy-k külön osztályban |
| **DRY** | Form Request újrafelhasználás (Store/Update); `makeFriends()` helper a tesztekben; közös Livewire komponensek |
| **Separation of Concerns** | Web vs API kontrollerek szétválasztása; Blade nézetek vs Livewire komponensek; Observer pattern |
| **Meaningful naming** | `getOrCreateDailyQuest()`, `completeQuest()`, `addByUsername()`, `toggleAdmin()` |
| **Consistent conventions** | camelCase metódusok, snake_case DB oszlopok, PascalCase osztályok |

### Architektúra rétegek
```
Routes (web.php / api.php)
  └── Controllers (Web / Api)
        ├── Form Requests (validáció)
        ├── Policies (authorizáció)
        ├── Models (Eloquent ORM)
        │     ├── Observers (lifecycle hooks)
        │     ├── Factories (test data)
        │     └── Relationships
        └── Views
              ├── Blade templates
              └── Livewire components
```

### Laravel best practices alkalmazása
- **Eloquent ORM:** Nincs raw SQL — minden query Eloquent builder-rel
- **Route Model Binding:** `Jar $jar`, `User $user` — automatikus ID → modell feloldás
- **Mass Assignment Protection:** `$fillable` minden modellben
- **Form Request Validation:** Validáció kiszervezve dedikált osztályokba
- **Soft Deletes:** Adatvesztés megelőzése
- **Policy-based Authorization:** `$this->authorize()` hívások a kontrollerekben
- **Observer pattern:** Automatikus `created_by` beállítás
- **Attribute Accessors:** `getProfilePictureUrlAttribute()`, `getPictureUrlAttribute()`
- **Factory + Seeder:** Tesztadatok generálása

---

## 6. Tesztelés (3 pont)

### Teszt környezet
- **PHPUnit 11** + Laravel test framework
- **In-memory SQLite** adatbázis — tesztek nem érintik az éles DB-t
- **RefreshDatabase trait** — minden teszt tiszta állapotból indul
- **Model Factories** — `UserFactory`, `JarFactory` — konzisztens tesztadatok

### Teszt lefedettség

```
Tests:    39 passed (72 assertions)
Duration: ~3s
```

| Teszt fájl | Tesztek száma | Tesztelt terület |
|---|---|---|
| `ExampleTest` | 2 | App boot, home page betöltés |
| `JarTest` | 10 | CRUD + join authorizáció |
| `FriendTest` | 7 | Barátkérés lifecycle |
| `MessageTest` | 8 | Chat auth + validáció |
| `JarInvitationTest` | 7 | Meghívó lifecycle + lejárat |
| `JarQuestTest` | 5 | Quest CRUD authorizáció |

### Tesztelési minta — Happy path / Sad path

Minden feature-höz minimum 3 teszt:
1. **✅ Happy path** — sikeres művelet autorizált felhasználóval
2. **❌ Unauthorized path** — 403 Forbidden nem jogosult felhasználónak
3. **❌ Validation path** — 422 érvénytelen adatokra

**Példa (Jar törlés):**
```php
// ✅ A jar létrehozója törölheti
public function test_jar_creator_can_delete_their_jar(): void
{
    $user = User::factory()->create();
    $jar  = Jar::factory()->create(['created_by' => $user->id]);
    $response = $this->actingAs($user)->delete(route('jars.destroy', $jar));
    $response->assertRedirect(route('home'));
    $this->assertSoftDeleted('jars', ['id' => $jar->id]);
}

// ❌ Más felhasználó nem törölheti
public function test_non_creator_cannot_delete_a_jar(): void
{
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $jar   = Jar::factory()->create(['created_by' => $owner->id]);
    $response = $this->actingAs($other)->delete(route('jars.destroy', $jar));
    $response->assertForbidden();
}
```

### Speciális tesztelési technikák
- **Idő manipuláció:** Meghívó lejárat tesztelése `DB::table()->update(['updated_at' => now()->subMinutes(10)])`
- **JSON API tesztelés:** `postJson()` a validációs hibák 422 kóddal való visszaadásához
- **Helper metódusok:** `makeFriends()` — újrafelhasználható barátság setup

### Futtatás
```bash
php artisan test                          # Összes teszt
php artisan test --filter=JarTest         # Egy teszt fájl
php artisan test --filter=test_user_can_send_a_message_to_a_friend  # Egy metódus
```

---

## 7. Gyors referencia — Védés közben elhangozható kérdések

### "Miért Laravel-t választottátok?"
> Laravel a legnépszerűbb PHP keretrendszer, beépített ORM-mel, migrációkkal, validációval, és Sanctum-mal az API auth-hoz. A Livewire lehetővé teszi a reaktív UI-t szerver-oldalról, JavaScript keretrendszer nélkül.

### "Hogyan működik az autentikáció?"
> Kétféle: a webes felület session-alapú (Laravel UI), az API Sanctum token-alapú. Regisztrációnál/bejelentkezésnél a szerver egy Bearer tokent ad vissza, amit a kliens minden kéréshez csatol az `Authorization` headerben. A token 1 hétig érvényes, device-specifikus.

### "Hogyan biztosítjátok a biztonságot?"
> - CSRF védelem minden web form-on
> - Sanctum token auth az API-n
> - Policy-based authorization (14 policy osztály)
> - Form Request validáció (dedicated osztályok)
> - Password hashing (bcrypt)
> - Mass assignment protection ($fillable)
> - Soft deletes (adatvesztés ellen)
> - Input sanitization és max length validáció

### "Mi a különbség a web és az API kontroller között?"
> A web kontrollerek Blade nézeteket adnak vissza (redirect + session flash), az API kontrollerek JSON-t adnak vissza HTTP státusz kódokkal (200, 201, 403, 409, 422). Mindkettő ugyanazokat a modelleket és policy-ket használja.

### "Hogyan működik a napi quest?"
> A `getOrCreateDailyQuest()` metódus ellenőrzi, hogy a mai napra van-e már kiválasztott quest. Ha nincs, véletlenszerűen választ egyet (az előzőt kizárva a duplikáció elkerülése érdekében), és frissíti a jar `current_daily_quest_id` mezőjét.

### "Milyen egyedi, komplex algoritmusokat emelnétek ki?"
> - **Idő-alapú meghívó (TTL):** Cron job-ok helyett lazy validáció az 5 perces lejárathoz.
> - **Képoptimalizálás:** Kliensoldali Cropper.js base64 → szerveroldali .webp konverzió.
> - **Admin Bypass:** A Policy-k `before` metódusa minden jogosultság-ellenőrzés előtt azonnali hozzáférést ad az adminoknak.
> - **Egyesített idővonal:** Különböző táblák (üzenetek, meghívók) összefésülése a memóriában.

### "Hogyan skálázható az alkalmazás?"
> - Pagination az API végpontokon (10-20 elem/oldal)
> - Eager loading (`with()`) az N+1 query probléma elkerülésére
> - In-memory SQLite tesztelés (gyors CI/CD)
> - Soft deletes (archiválás törlés helyett)
> - Separate API réteg (mobil kliens bármikor cserélhető)

---

## 8. Fájl struktúra összefoglaló

```
SideQuestJar/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/           ← 7 API kontroller (JSON)
│   │   │   ├── Auth/          ← 6 Auth kontroller
│   │   │   ├── JarController  ← Web kontroller (Blade)
│   │   │   ├── FriendController
│   │   │   ├── MessageController
│   │   │   ├── UserController
│   │   │   └── LikeController
│   │   └── Requests/          ← 4 Form Request (validáció)
│   ├── Models/                ← 14 Eloquent modell
│   ├── Observers/             ← 3 Observer
│   └── Policies/              ← 14 Policy (authorizáció)
├── database/
│   ├── factories/             ← 11 Factory
│   ├── migrations/            ← 18 Migráció
│   └── seeders/               ← 12 Seeder
├── resources/views/
│   ├── components/            ← 10+ Livewire komponens
│   ├── layouts/               ← App layout + default
│   ├── auth/                  ← Login, Register, Password Reset
│   ├── chat/                  ← Chat index + show
│   ├── friends/               ← Friends index
│   ├── jars/                  ← Jar CRUD nézetek
│   └── users/                 ← Profil nézetek
├── routes/
│   ├── web.php                ← 25+ web route
│   └── api.php                ← 30+ API route
├── tests/Feature/             ← 6 teszt fájl, 39 teszt
├── SETUP.md                   ← Telepítési útmutató
├── TESTING.md                 ← Tesztelési dokumentáció
└── DEFENCE.md                 ← Ez a fájl
```

---

## 9. Számokban

| Metrika | Érték |
|---|---|
| Modellek | 14 |
| Migrációk | 18 |
| Kontrollerek (Web) | 8 |
| Kontrollerek (API) | 7 |
| Policy-k | 14 |
| Livewire komponensek | 13+ |
| Form Request-ek | 4 |
| Factory-k | 11 |
| Seeder-ek | 12 |
| Observer-ek | 3 |
| Tesztek | 39 (72 assertion) |
| API végpontok | 30+ |
| Web route-ok | 25+ |
| Blade nézetek | 20+ |

---

## 10. Továbbfejlesztési lehetőségek (Jövőkép)

Bár az alkalmazás jelen formájában egy teljes értékű MVP (Minimum Viable Product), a jövőben több irányba is továbbfejleszthető:

- **Natív Mobilalkalmazás:** A meglévő REST API használatával (React Native, Flutter vagy MAUI) dedikált okostelefonos alkalmazás készítése.
- **Valós idejű WebSocketek:** A chat és az értesítések átállítása AJAX polling-ról WebSocket (pl. Laravel Reverb) alapúra, a még gyorsabb működés érdekében.
- **AI Tartalomgenerálás:** ChatGPT API integrálása, amivel a felhasználók automatikusan generálhatnak 30 napos kihívásokat (Questeket) egyetlen kattintással.
- **Monetizáció:** Prémium "Pro" funkciók (pl. korlátlan privát Jar-ok) bevezetése a fenntartható működés érdekében.
