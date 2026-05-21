# .NET MAUI Blazor Hybrid Kliensalkalmazás Részletes Dokumentációja

Ez a dokumentum a vizsgaremek MAUI kliensének belső működését, architektúráját, és a Laravel backenddel való integrációját mutatja be részletesen. A rendszer nem a hagyományos XAML-alapú megközelítést alkalmazza, hanem a modern **MAUI Blazor Hybrid** technológiára épít.

## 1. Rendszerarchitektúra és a Blazor Hybrid Modell

A projekt magját a `MainPage.xaml` fájlban található `BlazorWebView` adja. Ez az elem hidat képez a natív operációs rendszer (Windows, Android, iOS, macOS) és a webes technológiák (HTML, CSS, C# Razor) között.

*   **Hostolás:** A `MainPage.xaml` betölti a `wwwroot/index.html` fájlt, ami elindítja a Blazor WebAssembly/Server alapú alkalmazást a lokális natív konténeren belül.
*   **Routing (Útválasztás):** A navigációt a beépített Blazor Router végzi a `Routes.razor` fájlon keresztül. Nem a MAUI hagyományos Shell-alapú navigációját (`AppShell.xaml`) használjuk a nézetek közötti váltásra, hanem a Blazor saját komponens-alapú routing-ját (pl. a `Home.razor` fájl tetején lévő `@page "/"` definíciót).
*   **UI Keretrendszer:** A megjelenésért a **Blazorise** könyvtár felel, amely Bootstrap 5 stílusokat és ikonokat használ. Ezzel a megoldással a felhasználói felület automatikusan reszponzív, és egységes megjelenést biztosít minden platformon extra XAML-kódolás nélkül.

## 2. Megjelenítés és Üzleti Logika (UI és MVVM)

A felhasználói felületet `.razor` kiterjesztésű fájlok (nézetek, azaz View-k) építik fel a `Views` mappában. A projekt érdekessége, hogy a Razor komponenseket ötvözi a klasszikus **MVVM (Model-View-ViewModel)** tervezési mintával.

### A Razor Nézetek (View)
Egy tipikus nézet, például a `Home.razor` vagy a `JarDetails.razor`, tartalmazza a HTML/Bootstrap jelölőket (markup). A C# logikát a fájl alján található `@code` blokkon belül definiáljuk.
A nézetek függőségeit (Service-ek, ViewModel-ek) a fájl elején az `@inject` direktívával injektáljuk a Dependency Injection (DI) konténerből (pl. `@inject JarService JarService`). A DI konténert a `MauiProgram.cs` konfigurálja.

### A ViewModel Réteg
Bár a Blazor önmagában is képes kezelni a lokális állapotot a nézeten belül, az üzleti logikákat a `ViewModels` mappába szerveztük (pl. `HomeViewModel`, `AuthViewModel`, `ProfileViewModel`).
Ezek az osztályok a Microsoft által támogatott `CommunityToolkit.Mvvm` csomagra épülnek:
*   Az osztályok a `ObservableObject` ősosztályból származnak.
*   Az állapotokat (változókat) az `[ObservableProperty]` attribútummal jelöljük meg (pl. `[ObservableProperty] public partial User? CurrentUser;`). A forráskód-generátor ebből automatikusan implementálja az `INotifyPropertyChanged` interfészt, így amikor egy változó értéke megváltozik, a felület (a View) azonnal értesítést kap róla.
*   Az aszinkron parancsokhoz a `[RelayCommand]` attribútumot használjuk.

### Adatkötés (Data Binding)
A XAML-ben megszokott `{Binding PropertyName}` szintaxis helyett itt a tisztább Blazor szintaxist használjuk az összekötésre:
*   Változók megjelenítése (One-way binding): `@DiscoverData.MainJar.Name`
*   Kétirányú adatkötés inputoknál és űrlapelemeknél (Two-way binding): `@bind="claimDescription"`
*   Eseménykezelés (Event binding): `@onclick="SubmitClaim"`

## 3. Hálózati Kommunikáció és DTO-k (Data Transfer Objects)

A Laravel backend és a kliens közötti adatcsere kizárólag **JSON** formátumban, egy RESTful API-n keresztül történik. Ezt a hálózati forgalmat a `Services` mappában található osztályok absztrahálják el (pl. `JarService`, `AuthService`, `FriendService`).

### HTTP Kérések és Autentikáció
A hálózati réteg alapja a beépített `HttpClient`. Az API végpontokat aszinkron módon, a szálakat nem blokkoló `async/await` hívásokkal érjük el, így a felhasználói felület sosem fagy meg (Responsive UI).
Autentikációt igénylő kérések esetén (amelyeket a Laravel oldalon pl. a Sanctum véd) a `SetAuthorizationHeaderAsync` metódus a MAUI `SecureStorage` osztályából (titkosított, operációs rendszer szintű helyi kulcstartóból) kiolvassa a bejelentkezéskor kapott tokent. Ezt a tokent fűzi hozzá minden védett HTTP kérés fejlécéhez (`Authorization: Bearer <token>`).

### A JSON Deszerializáció és a Kígyó-Teve (Snake-Pascal) Konverzió
A Laravel backend a PHP és az adatbázisok konvenciói szerint `snake_case` formátumban küldi a mezőket (pl. `created_by`, `likes_count`). A C# viszont a Microsoft konvenciók alapján a `PascalCase` formátumot (pl. `CreatedBy`, `LikesCount`) követeli meg.
A probléma elegáns megoldására a Service-ek konstruktorában konfigurálva van a `System.Text.Json` csomag:
```csharp
JsonSerializerOptions jsonOptions = new JsonSerializerOptions
{
    PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower
};
```
Ezzel az automatikus konverzióval elkerültük, hogy a C# DTO és Modell osztályokban minden egyes property-t külön-külön manuálisan fel kelljen annotálni a `[JsonPropertyName("snake_case_nev")]` attribútummal. Az Enum-ok szerializációjához (pl. Jar típusok) pedig a `JsonStringEnumConverter(JsonNamingPolicy.SnakeCaseLower)` felel.

### DTO-k (Data Transfer Objects) vs Modellek
*   A `Models` mappában lévő osztályok (pl. `Jar`, `User`, `JarQuest`) megegyeznek az adatbázis alapvető entitásaival. Ezeket az Entity Framework is használja.
*   A `DTOs` mappában lévő osztályok (pl. `DiscoverResponse`, `PaginatedResponse`) speciális "borítékok". Egy API válasz ugyanis gyakran bonyolultabb, mint egyetlen Modell. Egy `DiscoverResponse` egyszerre hozhatja le a `MainJar`-t (Modell), a `MainQuest`-et, és két listát (`PopularJars`, `RecommendedJars`). A DTO-k arra szolgálnak, hogy ezeket a komplex, mély JSON struktúrákat hiba nélkül le tudjuk képezni C# objektumokká.

## 4. Képfeltöltés, Adatbázis és Offline Működés

### Képfeltöltés (MultipartFormDataContent)
Amikor a felhasználó egy fájlt (pl. képet) tölt fel (például egy quest teljesítésekor a `Home.razor` nézetből), az alkalmazás beolvassa a kép byte-tömbjét a memóriába (illetve Base64 formátumban is kezeli a kivágáshoz - CropperJS). 
A backend felé nem egy egyszerű string JSON-t küldünk el, hanem a `JarService`-ben egy `MultipartFormDataContent` típusú HTTP POST kérést építünk fel. Ebbe a speciális csomagba külön `StringContent`-ként kerül bele a szöveges leírás (`description`), és `ByteArrayContent`-ként maga a bináris képfájl (`picture`), megadva a pontos MIME-típust (`image/jpeg`). A Laravel ezt egy normál fájlfeltöltésként értelmezi.

### Offline Működés (Offline Fallback - Kritikus funkció!)
A projekt felépítésének egyik legrobusztusabb eleme az offline működés biztosítása. Ha a felhasználó egy erdőben sétál internet nélkül, az alkalmazásnak akkor is működnie kell bizonyos keretek között. Ezt a következő architektúrával oldottuk meg:

1.  **Helyi SQLite Adatbázis:** A `Data/AppDbContext.cs` beállítja az Entity Framework Core-t (EF Core). Ez a könyvtár létrehoz és kezel egy lokális, a mobil/asztali eszközön tárolt SQLite adatbázis fájlt. Tartalmazza a relációs táblákat (pl. `Users`, `Jars`, `Quests`, `JarUsers`).
2.  **Automatikus Gyorsítótárazás (Caching):** Minden alkalommal, amikor az alkalmazás sikeresen és frissen lekérdez adatokat a Laravel backendről (például betölti a publikus jar-okat), a Service-ben a háttérben meghívódik a `CacheJarsAsync` (és hasonló) metódus. Ez beírja vagy frissíti ezeket az adatokat a helyi SQLite adatbázisban (upsert művelet).
3.  **Hálózati Hibakezelés és Visszaesés (Fallback):** A HTTP hívások (pl. `GetDiscoverDataAsync`) egy `try-catch` blokkba vannak csomagolva. Ha a készülék offline van, vagy a szerver elérhetetlen, a kérés kivételt dob (`HttpRequestException`). A `catch` ág ilyenkor nem állítja le hibával az alkalmazást, hanem némán megnyitja a helyi **AppDbContext** adatbázist, LINQ lekérdezésekkel előveszi a korábban gyorsítótárazott adatokat, majd azokat küldi vissza a felületnek (A UI és a ViewModel nem is tudja, hogy az adat honnan jött). A felhasználó így internet nélkül is meg tudja nézni a letöltött Jar-jait és feladatait, vagy akár offilne módban rögzítheti a csatlakozást/teljesítést, amit az alkalmazás később (a local db alapján) szinkronizálhat.
