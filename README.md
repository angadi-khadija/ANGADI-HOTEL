# Angadi Hotel — beginner's guide to PHP and SQL

This guide explains how to start the site, how its PHP admin sign-in works, and what the SQL database commands mean. You do not need to understand all of the code to use the site. Read the setup instructions first, then use the explanations as a reference.

## 1. A few basic words

- **Website/browser:** The browser displays the pages you visit.
- **HTML:** The content and structure of a page: headings, links, forms, and buttons.
- **CSS:** The visual design: colors, spacing, fonts, and layout.
- **JavaScript:** Code that runs in the browser. The booking enquiry demo uses it.
- **PHP:** Code that runs on the web server. The browser does not run PHP itself. This project uses PHP for admin sign-in, session checks, and sign-out.
- **MySQL:** The database program. It stores tables and the information in those tables.
- **Database:** A named collection of related tables. This project's database is named `choise_hotel`.
- **Table / row / column:** A table is like a spreadsheet. A row is one record; a column is one piece of information about each record.
- **Admin:** A hotel administrator whose account is in the `admins` table and is marked `active`.
- **Password hash:** A one-way representation of a password. The site stores this instead of storing the original password.
- **Server:** The computer program that runs PHP and sends the resulting page to the browser. XAMPP supplies Apache for this project.

The dot in a PHP variable name is not special: `$admin . ' ' . $name` joins text together. A semicolon `;` usually ends a PHP statement. Curly braces `{ }` group instructions together.

### PHP punctuation used in this project

- `$name` is a variable: a named place to keep a value.
- `$_POST`, `$_SESSION`, and `$_SERVER` are built-in PHP arrays. `$_POST` contains submitted form values, `$_SESSION` contains values remembered between requests, and `$_SERVER` contains information about the current request.
- `=` puts a value into a variable or array entry. For example, `$errorMessage = '';` stores empty text.
- `===` means “is exactly equal to,” including the value's type. `!==` means “is not exactly equal to.”
- `&&` means “and”; `||` means “or”; `!` means “not.”
- `??` means “use the value on the left if it exists; otherwise use the value on the right.”
- `condition ? valueA : valueB` is a short if/else expression: choose `valueA` when the condition is true, otherwise choose `valueB`.
- `.` joins text together. `->` accesses a method or property on an object, such as `$statement->execute(...)`.
- `::` accesses a class constant, such as `PDO::ATTR_ERRMODE`.
- `=>` connects a key to its value in an array, such as `'email' => $email`.
- `|` combines option values, as in `ENT_QUOTES | ENT_SUBSTITUTE`.
- `()` hold a function's inputs; `[]` make an array or select one of its values; `{}` group the instructions controlled by a function, condition, or loop.
- `//` starts a one-line PHP comment. `/* ... */` surrounds a longer comment. Comments explain code and are not run.

## 2. Start the website with XAMPP

PHP pages must be opened through Apache. Do **not** double-click `login.php` or open it with a `file://` address; that would bypass the PHP server.

1. Keep the project folder inside XAMPP's `htdocs` folder.
2. Open **XAMPP Control Panel**.
3. Click **Start** beside **Apache** and **MySQL**. Apache serves the website; MySQL provides the database.
4. Open this site in your browser: `http://localhost/Nouveau%20dossier/`. `%20` represents the space in the folder name.
5. To sign in as an admin, open `http://localhost/Nouveau%20dossier/login.php`.

If PowerShell says `php` is not recognized, PHP is not on that terminal's PATH. Use the full path to your PHP executable instead, for example:

```powershell
C:\xampp\php\php.exe -r "echo password_hash('REPLACE_WITH_A_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
```

The path can differ between computers; use the `php.exe` that came with your XAMPP installation.

The `hotel` database schema was imported for this project. If you need to import it again on a different computer, use phpMyAdmin:

1. Open `http://localhost/phpmyadmin/`.
2. Choose the **Import** tab.
3. Select this project's `database.sql` file and click **Import** or **Go**.

**Important:** the SQL uses `IF NOT EXISTS`. That makes it safe to run when the database and tables already exist, but it does not update an existing table if you later change its design.

## 3. What each important file does

- [`index.html`](./index.html), [`rooms.html`](./rooms.html), and [`booking.html`](./booking.html): public guest pages.
- [`css/site.css`](./css/site.css): styling shared by the pages.
- [`js/site.js`](./js/site.js): browser interactions, including the demo booking form.
- [`login.php`](./login.php): admin sign-in page and form processing.
- [`admin-auth.php`](./admin-auth.php): shared PHP functions for sessions, database access, CSRF tokens, and checking admin access.
- [`dashboard.php`](./dashboard.php): admin-only dashboard page.
- [`logout.php`](./logout.php): safely ends the admin session.
- [`database.sql`](./database.sql): instructions that create the MySQL database and tables.
- [`register.html`](./register.html): explains that staff accounts are managed by the hotel owner; it does not create accounts.

## 4. Create an admin account

The database has an `admins` table, but it starts empty. No default email or password has been created. You must add an admin before anyone can sign in.

### Step A: make a password hash

Open **PowerShell** and run this command, replacing `REPLACE_WITH_A_PASSWORD` with a password you choose:

```powershell
php -r "echo password_hash('REPLACE_WITH_A_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
```

What the command means:

- `php` starts PHP from the command line.
- `-r` tells PHP to run the short PHP instruction that follows, without making a PHP file.
- `echo` prints a result on the screen.
- `password_hash(...)` turns the password into a hash that can be stored safely.
- `PASSWORD_DEFAULT` asks PHP to use its recommended password-hashing method.
- `PHP_EOL` prints a line break after the result.

The printed value will look like a long string, often beginning with `$2y$` on current PHP versions. Copy that **whole value**. It is the hash, not the password. Keep the password private; do not paste it into a public chat or save it in the database.
The example puts the password text into the command itself, which may remain in terminal history. Do not use a real password on a shared computer.

### Step B: insert the admin row

1. In phpMyAdmin, select the `choise_hotel` database.
2. Open the **SQL** tab.
3. Paste this query. Replace the sample name and email, and replace `PASTE_HASH_HERE` with the hash from Step A.

```sql
INSERT INTO admins (first_name, last_name, email, password_hash, role, account_status)
VALUES ('Hotel', 'Administrator', 'admin@example.com', 'PASTE_HASH_HERE', 'owner', 'active');
```

What this SQL does:

- `INSERT INTO admins` means “add a new row to the `admins` table.”
- The parentheses after `admins` list the columns that will receive values.
- `VALUES` lists one value for each column, in the same order.
- `'Hotel'` is the first name; `'Administrator'` is the last name.
- `'admin@example.com'` is the email to type into the sign-in form.
- `'PASTE_HASH_HERE'` must be the generated hash, not the plain password.
- `'owner'` is the admin's role label.
- `'active'` enables the account so it can sign in.
- The semicolon `;` marks the end of this SQL command.

Click **Go**. Then visit `login.php`, enter that email and the original password, and click **Sign in to dashboard**.

## 5. How the PHP sign-in works

The form sends its email, password, and a security token to `login.php`. PHP checks the information on the server and redirects a valid admin to the dashboard. It does not send an invalid user to the guest home page.

### `login.php`, step by step

1. `<?php` tells Apache that PHP code starts here.
2. `declare(strict_types=1);` asks PHP to be stricter about data types, helping catch programming mistakes.
3. `require_once __DIR__ . '/admin-auth.php';` loads the shared authentication functions. `__DIR__` means “the folder containing this file”; `require_once` loads the file once and reports an error if it cannot be loaded.
4. `start_admin_session();` starts or resumes the browser's admin session. A session lets the server remember that the admin signed in.
5. `if (isset($_SESSION['admin_id']))` checks whether this session already has an admin ID.
6. `header('Location: dashboard.php');` tells the browser to go to the dashboard. `exit;` stops the current PHP file so it cannot continue showing the login form.
7. `$errorMessage = '';` creates an empty message. PHP variables begin with `$`.
8. `$_SERVER['REQUEST_METHOD'] === 'POST'` checks whether the browser submitted the form. `POST` is the form method used to send the credentials to the server.
9. `$_POST['email']` and `$_POST['password']` are values sent by the form. `?? ''` means “use an empty string if this value was not sent.”
10. `trim(...)` removes extra spaces around the email; `strtolower(...)` converts it to lowercase. The `(string)` part converts the value to text.
11. The submitted CSRF token is read from the form in the same way. CSRF is a type of unwanted request; this token helps make sure the form came from this site's own sign-in page.
12. `hash_equals(...)` safely compares the form token with the token stored in the session. If they do not match, PHP displays a session-expired message instead of checking the password.
13. `filter_var($email, FILTER_VALIDATE_EMAIL)` checks that the email has a valid email format. `||` means “or.” An empty password is also rejected.
14. `try { ... } catch (PDOException $exception) { ... }` tries the database operation and handles a database-specific error if one occurs.
15. `admin_database()` opens the MySQL connection. `prepare(...)` prepares a database query with a placeholder instead of inserting the email directly into the query.
16. The SQL query selects the admin ID, name, email, password hash, and role from `admins`. Its `WHERE` rule requires the submitted email and `account_status = 'active'`. `LIMIT 1` asks for at most one matching row.
17. `$statement->execute(['email' => $email]);` safely supplies the email value for `:email`. The `:email` placeholder is defined in the query.
18. `$statement->fetch()` reads the matching row. If there is no matching active admin, it returns no admin record.
19. `if ($admin && password_verify(...))` requires both a matching row and a password that matches the saved hash. `password_verify()` compares the password without needing to turn the hash back into the original password.
20. `session_regenerate_id(true);` gives the signed-in admin a new session ID. This helps protect against session-fixation attacks.
21. The three `$_SESSION[...] = ...` lines remember the admin ID, displayed name, and role for later requests.
22. `unset($_SESSION['csrf_token']);` removes the sign-in form token after successful sign-in.
23. `header('Location: dashboard.php'); exit;` redirects a valid admin to the dashboard and stops the login script.
24. If the row or password does not match, `$errorMessage` is set to the same generic message. This avoids revealing whether a particular email address exists.
25. The `catch` block writes the detailed database error to the server error log with `error_log(...)`, but displays only a general message to the visitor.
26. `$csrfToken = admin_csrf_token();` creates or retrieves the token that the login form will include.
27. `?>` ends the PHP section. The rest of the file is HTML, with a few PHP expressions that insert safe values into the page.

### The login page's HTML and embedded PHP

- `<!doctype html>` tells the browser to use modern HTML rules.
- `<html lang="en">` starts the page and identifies its language as English.
- `<head>` contains page information that is not the main visible content.
- `<meta charset="utf-8">` enables standard text characters. The viewport setting makes the page adapt to mobile screens.
- `<title>` sets the browser tab title. The stylesheet link loads `css/site.css`.
- `<body>` contains visible page content. `<main>` marks the main content area.
- The `<a>` elements are links. The brand link returns to `index.html`.
- `<h1>` is the main heading; `<p>` is a paragraph.
- `<?php if ($errorMessage !== ''): ?>` means “show the following HTML only when there is an error message.” `<?php endif; ?>` ends that condition.
- `htmlspecialchars(...)` converts special characters before displaying text in HTML. This helps prevent text from being treated as executable HTML.
- `<form action="login.php" method="post">` submits the form back to `login.php` using POST.
- The hidden `csrf_token` input sends the security token without showing it as a field.
- `<label for="email">` names the email field. Its `for` value matches the input's `id`.
- `name="email"` and `name="password"` are the keys PHP reads from `$_POST`.
- `type="email"` and `type="password"` ask the browser to validate email format and conceal the password as it is typed.
- `required` asks the browser to require a value before submitting.
- The email input's `value="<?php echo ... ?>"` keeps the email visible after a failed submission. `echo` outputs text; `htmlspecialchars` makes that output safe.
- The submit `<button>` sends the form. The last link returns to the guest website.

## 6. The shared PHP file: `admin-auth.php`

This file defines functions used by more than one page. A **function** is a named group of instructions that can be run when needed. `: void` means the function does not return a result.

### `start_admin_session()`

- `session_status() === PHP_SESSION_ACTIVE` checks whether PHP already has a session running. If so, `return;` exits the function to avoid starting it twice.
- `$_SERVER['HTTPS']` checks whether the site is being visited over HTTPS. The `secure` cookie setting is enabled when HTTPS is being used.
- `session_set_cookie_params([...])` sets browser-cookie options for the session:
  - `lifetime => 0`: the cookie lasts for the browser session.
  - `path => '/'`: the cookie is available throughout this site.
  - `secure => $isSecure`: send it only over HTTPS when HTTPS is enabled.
  - `httponly => true`: browser JavaScript cannot read it.
  - `samesite => 'Lax'`: limits some cross-site requests.
- `session_start();` starts or resumes the session.

### `admin_database()`

- `getenv('HOTEL_DB_HOST')` asks the server for an optional database setting. `?:` means “if it is missing or empty, use the value after `?:`.”
- `$host`, `$database`, and `$username` default to `localhost`, `choise_hotel`, and `root`.
- The password comes from `HOTEL_DB_PASSWORD`; on a typical local XAMPP install it defaults to an empty string.
- The `? '' : $password` part is a ternary expression: if `getenv()` says the password setting is missing (`false`), use an empty password; otherwise use the configured password.
- `function admin_database(): PDO` says this function returns a PDO database-connection object. `PDO` is PHP's database connection library.
- `new PDO(...)` creates PHP's connection to MySQL. The connection string specifies host, database name, and UTF-8 character encoding.
- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` makes database errors raise an exception that PHP can handle.
- `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` returns database rows with column names, such as `$admin['email']`.
- `PDO::ATTR_EMULATE_PREPARES => false` asks MySQL to use real prepared statements.
- `return new PDO(...)` gives the connection back to the code that requested it.

### `admin_csrf_token()`

- `isset($_SESSION['csrf_token'])` checks whether this session already has a token.
- `bin2hex(random_bytes(32))` makes 32 random bytes and converts them to readable hexadecimal text.
- The token is saved in the session and returned to the page so it can be placed in a hidden form field.

### `require_admin()`

- `filter_var(..., FILTER_VALIDATE_INT)` checks that the session's admin ID is an integer.
- If there is no valid ID, `header('Location: login.php')` redirects to sign-in and `exit;` stops the page.
- The function queries `admins` for that ID, and also requires `account_status = 'active'`. This means disabling or deleting an account prevents it from continuing to use the dashboard.
- `execute(...)` supplies the admin ID for the `:admin_id` placeholder. `fetch()` reads the row.
- If no active row exists, `$_SESSION = [];` empties the session, `session_regenerate_id(true)` replaces its ID, and the browser is redirected to login.
- If the admin is still active, the function refreshes the name and role stored in the session.

## 7. The protected page: `dashboard.php`

The PHP section at the top runs before any dashboard HTML is sent:

1. `require_once` loads the shared functions.
2. `start_admin_session()` starts or resumes the session.
3. `require_admin()` blocks visitors without an active admin account.
4. `admin_csrf_token()` creates the token needed for the sign-out form.
5. `htmlspecialchars(...)` prepares the admin's name and role for safe display in HTML.
6. `?>` ends PHP and lets the browser receive the dashboard HTML.

The sign-out form posts its hidden CSRF token to `logout.php`. The rest of the dashboard uses HTML, CSS, and JavaScript for tabs, search, and filters. Its displayed hotel figures are sample data; signing in does not make those figures database-backed.
The role is currently displayed as a label only. At the moment, every active admin can open the same dashboard; the site does not yet give owners, managers, and receptionists different permissions.

## 8. Sign-out: `logout.php`

1. `require_once` loads the shared functions, and `start_admin_session()` starts the current session.
2. The `if` statement requires both a POST request and a matching CSRF token. `!==` means “not equal.” `||` means “or.”
3. `http_response_code(400)` marks an invalid request as a bad request. `exit(...)` stops the script and displays the short error.
4. `$_SESSION = [];` removes the session's saved admin information.
5. `ini_get('session.use_cookies')` checks whether sessions use browser cookies.
6. `session_get_cookie_params()` reads the current cookie settings. `setcookie(...)` expires the session cookie in the browser.
7. `session_destroy()` removes the server-side session.
8. The `header(...)` line sends the browser back to `login.php`; `exit;` stops the script.

## 9. Understanding `database.sql`

SQL is the language used to ask MySQL to create databases, tables, and rows.

### First statements

- `CREATE DATABASE IF NOT EXISTS choise_hotel`: create a database named `choise_hotel`, but do not report an error if it already exists.
- `CHARACTER SET utf8mb4`: allow a wide range of text characters, including accented characters and emoji.
- `COLLATE utf8mb4_unicode_ci`: choose how text is compared and sorted. `ci` means comparisons are generally case-insensitive.
- `USE choise_hotel`: make that database the target for the following table-creation commands.

### SQL words used throughout

- `CREATE TABLE IF NOT EXISTS`: create a table only if it does not already exist.
- `NOT NULL`: a value is required.
- `NULL`: a value may be left empty.
- `AUTO_INCREMENT`: MySQL chooses the next number for a new ID.
- `PRIMARY KEY`: the unique identifier for a row.
- `UNIQUE`: do not allow two rows to have the same value in this column.
- `VARCHAR(100)`: text up to 100 characters. The number in parentheses is the maximum length.
- `INT`, `INT`, `SMALLINT`, and `TINYINT`: whole-number types of different sizes.
- `UNSIGNED`: only non-negative numbers are allowed.
- `DATE`: a calendar date.
- `TIMESTAMP`: date and time.
- `DECIMAL(10, 2)`: a number with up to 10 digits total and 2 digits after the decimal point; useful for prices.
- `ENUM(...)`: only one of the listed text values is allowed.
- `DEFAULT`: the value MySQL uses when an insert does not provide one.
- `KEY`: an index that can make searches faster.
- `FOREIGN KEY`: a rule connecting a column to a row in another table.
- `ENGINE=InnoDB`: use MySQL's storage engine that supports transactions and foreign-key rules.
- A comma separates columns or rules. A semicolon `;` ends a SQL statement.

### Tables and their columns

**`clients`** stores guest accounts:

- `client_id`: automatically assigned unique ID.
- `first_name`, `last_name`: guest's names.
- `email`: guest's email; it must be unique.
- `phone`: optional phone number.
- `password_hash`: password hash for a guest account, not the plain password.
- `date_of_birth`, `address_line`, `city`, `country`, `postal_code`: optional guest details.
- `account_status`: either `active` or `disabled`; new accounts default to `active`.
- `created_at`: when the row was created.
- `updated_at`: when the row was last changed; MySQL updates it automatically.
- `PRIMARY KEY (client_id)`: make `client_id` the guest row's unique ID.
- `UNIQUE KEY uq_clients_email (email)`: prevent duplicate guest email addresses.

**`admins`** stores staff accounts:

- `admin_id`: automatically assigned unique admin ID.
- `first_name`, `last_name`, `email`, and `phone`: admin contact details. Email must be unique.
- `password_hash`: the hashed admin password checked by `login.php`.
- `role`: one of `owner`, `manager`, or `receptionist`; defaults to `receptionist`.
- `account_status`: one of `active` or `disabled`; defaults to `active`.
- `last_login_at`: optional date and time of the last sign-in.
- `created_at` and `updated_at`: account creation and change times.
- `PRIMARY KEY (admin_id)`: make `admin_id` the unique admin identifier.
- `UNIQUE KEY uq_admins_email (email)`: prevent two admins from sharing an email.

**`rooms`** stores room information:

- `room_id`: automatically assigned unique room ID.
- `room_number`: the room's number; it must be unique.
- `room_type`: type or name of the room.
- `floor_number`: optional floor.
- `capacity`: maximum guests; defaults to 1.
- `nightly_rate`: price per night.
- `description`: optional longer description.
- `room_status`: `active`, `maintenance`, or `inactive`; defaults to `active`.
- `created_at` and `updated_at`: creation and change times.
- `PRIMARY KEY (room_id)`: make `room_id` the unique identifier.
- `UNIQUE KEY uq_rooms_number (room_number)`: prevent duplicate room numbers.
- `KEY idx_rooms_status_type (room_status, room_type)`: index to help searches by status and type.

**`reservations`** stores bookings:

- `reservation_id`: automatically assigned unique reservation ID.
- `client_id`: ID of the guest who booked.
- `room_id`: ID of the reserved room.
- `check_in` and `check_out`: the stay dates.
- `guest_count`: number of guests; defaults to 1.
- `nightly_rate`: the nightly price recorded for the booking.
- `total_amount`: the total charge.
- `reservation_status`: one of `pending`, `confirmed`, `checked_in`, `checked_out`, or `cancelled`; defaults to `pending`.
- `special_requests`: optional guest request.
- `created_at` and `updated_at`: creation and change times.
- `PRIMARY KEY (reservation_id)`: make `reservation_id` the unique reservation identifier.
- The `KEY idx_...` lines add indexes to help searches by guest, room and dates, or status.
- `fk_reservations_client` requires `client_id` to match an existing `clients.client_id`.
- `fk_reservations_room` requires `room_id` to match an existing `rooms.room_id`.
- `ON UPDATE CASCADE` follows an ID change in the referenced row. `ON DELETE RESTRICT` stops a guest or room from being deleted while a reservation refers to it.

## 10. Database connection settings and common problems

`admin-auth.php` uses these local defaults:

| Setting | Default | Meaning |
|---|---|---|
| `HOTEL_DB_HOST` | `localhost` | MySQL is on this computer |
| `HOTEL_DB_NAME` | `choise_hotel` | database name |
| `HOTEL_DB_USER` | `root` | MySQL user |
| `HOTEL_DB_PASSWORD` | empty | MySQL password |

These defaults are for a typical local XAMPP installation, not for a public production server. For a live website, use a dedicated MySQL user with only the permissions the site needs, set a strong password, and use HTTPS.

- **“Sign-in is unavailable”**: check that MySQL is running, the database exists, and the connection settings match.
- **“Invalid email or password”**: check that the admin row exists, the email is correct, the password hash was generated correctly, and the account is `active`.
- **The PHP code appears in the browser**: open the page through `http://localhost/...` and make sure Apache is running; do not use `file://`.
- **The dashboard sends you to login**: sign in first. The dashboard checks the admin account in MySQL on every page load.
- **A “database not found” error**: import `database.sql` into MySQL or correct `HOTEL_DB_NAME`.
- **`php` is not recognized in PowerShell**: use the full path to `php.exe` as shown above, or use the PHP command prompt supplied with your XAMPP installation.

## 11. What is and is not connected to MySQL

Admin sign-in and dashboard access are checked against the `admins` table. The dashboard's room, guest, and reservation figures are still illustrative sample content. Guest booking enquiries are stored in the current browser's local storage only; they are not saved to MySQL or sent to the hotel.
