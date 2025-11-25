# Ötletek: A Nagy Project

**Készítette a Nagy (Tori) Csapat:**
- Karácsony Zoltán
- Borza Gergő
- Nagy Panna Viktória

---

## 1. Projekt Leírás

Egy olyan asztali alkalmazást és hozzá tartozó internetes felületet készítünk, amely lehetőséget biztosít felhasználók kezelésére és biztonságos adattárolásra.

- **Alapfunkciók:** Regisztráció/Bejelentkezés, Adattárolás, manipuláció és törlés, biztonságos szerver elérés.
- **Fő koncepció (Side Quest Jar):** Egy asztali alkalmazás, amelyben a felhasználó naponta kap egy feladatot vagy ötletet.

---

## 2. A "Jar" (Ötletdoboz) Működése

A "Jar" egy ötletdoboz, amelyet valaki más hozott létre, saját feladatokkal és elgondolkodtató üzenetekkel teleírva. Az alkalmazás ebből választ ki véletlenszerűen egyet.

### Típusok:
- **Public Jar:** Weblapról letölthető, mások által készített dobozok.
- **Saját/Csoportos Jar:** Lehetőség van saját vagy közös ötletgyűjtésre.
    - *Public Group:* Olyan csoportos jar, amelyhez bármely felhasználó csatlakozhat és növelheti az ötletek listáját.

### Jutalmazási rendszer (Gamification):
- **Admin Jars:** Teljesítésükért "admin pontok" járnak -> beváltható *Merch* termékekre.
- **Egyéni/Csoport Jars:** Teljesítésükért "egyéni pontok" járnak -> *Streak* (sorozat) növelése.

---

## 3. Frontend és Felület Terv

### Általános felépítés
- **Navbar**
- **Footer**

### Oldalak szerkezete

#### 1. Belépés / Regisztráció
Hagyományos bejelentkezési és regisztrációs oldal.

#### 2. Kezdőlap (Főoldal)
- **Navigáció:** Fiók, Logout, Főoldal, Rólunk, Merch.
- **Design:** Legördülő menü a főoldaltól, "Munch-szerű" kártyás elrendezés.
- **Kategóriák:**
    - Admin Jar (Fő challenge)
    - Egyéni Jar
    - Csoport Jar

#### 3. Fiók (Profil)
- Profilkép
- Felhasználónév (Username)
- User szerkesztés
- Email cím
- Jelszó
- **Egyéb adatok:** Bankkártya adatok és Krisz lába

#### 4. Egyéb oldalak
- **Rólunk**
- **Merch:** Átlagos webshop/merch kinézet a termékeknek.
- **Logout:** Visszadob a kezdőoldalra.