# Proiect-Aplicatii-WEB-Avansate

### Descriere generală a proiectului:

Proiectul realizat este o aplicație web destinată gestionării unui service auto, implementată folosind PHP. Scopul principal al aplicației este de a automatiza și eficientiza procesele administrative ale unui service auto, precum gestionarea angajaților, clienților, pieselor, mașinilor, serviciilor, facturilor și programărilor. Aplicația permite interacțiunea cu baza de date pentru a adăuga, edita și șterge date specifice fiecărui tip de entitate, asigurându-se că datele sunt corecte și că respectă restricțiile de validitate impuse.

### Funcționalitățile principale implementate:

1. **Gestionare Angajați**:
   - Adăugare, editare și ștergere date angajați (CNP, adresă, etc.).
   - Posibilitatea de a actualiza informațiile angajaților în funcție de identificatorul lor unic (CNP).
   
2. **Gestionare Clienți**:
   - Adăugare, editare și ștergere date clienți (CNP, adresă, număr telefon, etc.).
   - Validarea CNP-ului clientului la adăugarea facturilor și a altor operațiuni.

3. **Gestiune Facturi**:
   - Vizualizare și gestionare facturi.
   - Validare CNP client pentru emiterea facturii. Factura nu poate fi emisă dacă CNP-ul clientului nu există în baza de date.

4. **Gestionare Mașini**:
   - Adăugare, editare și ștergere mașini (serie șasiu, marca, model, CNP proprietar).
   - Validare CNP proprietar la adăugarea unei mașini. Dacă CNP-ul nu există în baza de date, nu se poate adăuga mașina.

5. **Gestionare Piese**:
   - Adăugare, editare și ștergere piese auto (cost, denumire, etc.).

6. **Gestionare Programări**:
   - Programări pentru service.
   - Validare: nu se pot face programări în trecut.
   - Verificare serie șasiu pentru a asigura că mașina există în baza de date înainte de a efectua o programare.

7. **Gestionare Servicii**:
   - Adăugare, editare și ștergere servicii (denumire, cost).

### Instrucțiuni pentru instalare, configurare și utilizare:

1. **Instalare**:
   - Asigurați-vă că aveți un server local (de exemplu, XAMPP) sau un server web care suportă PHP și MySQL.
   - Descărcați fișierele proiectului și plasați-le în directorul corespunzător al serverului local (de obicei `htdocs` în XAMPP).
   - Creați o bază de date MySQL cu numele dorit și importați fișierele SQL corespunzătoare (tabelele și structura bazei de date).

2. **Configurare**:
   - Deschideți fișierul de configurare PHP (de obicei `config.php`) și setați conexiunea la baza de date (username, password, numele bazei de date).

3. **Utilizare**:
   - Accesați aplicația în browser prin `http://localhost/[numele_proiectului]`.
   - Navigați prin interfața web pentru a adăuga, edita și vizualiza angajați, clienți, mașini, piese, facturi, programări și servicii.


   - Finalizați adăugarea programării.

### Descrierea aplicației și scopul acesteia:

Aplicația este un sistem integrat pentru managementul unui service auto, destinat să ajute la urmărirea și organizarea activităților zilnice. Scopul principal este de a reduce erorile administrative și de a spori eficiența în gestionarea datelor clienților, angajaților, pieselor, mașinilor și serviciilor. Aplicația asigură validarea datelor și aplică reguli de business clare pentru a garanta corectitudinea informațiilor și evitarea proceselor incorecte.

### Funcționalitățile și tehnologiile utilizate:

- **Tehnologii**: PHP, MySQL, HTML, CSS.
- **Bază de date**: MySQL pentru stocarea datelor utilizatorilor, mașinilor, pieselor și facturilor.
- **Frontend**: Interfață prietenoasă și ușor de utilizat, cu validări și mesaje de eroare pentru utilizatori.
- **Backend**: Logica de business implementată în PHP, care gestionează adăugarea, editarea și validarea datelor.

### Alte informații relevante:

Proiectul a fost realizat pentru a oferi un sistem complet și funcțional pentru gestionarea unui service auto. Restricțiile implementate (de exemplu, validarea CNP-urilor, validarea seriei de șasiu și prevenirea programărilor în trecut) garantează că datele introduse sunt corecte și că toate procesele administrative sunt conforme cu regulile și cerințele service-ului auto. Aplicația poate fi extinsă și adaptată pentru a satisface nevoi suplimentare sau pentru a integra noi funcționalități.
