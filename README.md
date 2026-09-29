<div align="center">

# Spill The Tea 🍵

**A soft, silly meme gallery. Browse, share and react to memes.**

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-database-4479A1?logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-markup-E34F26?logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-styling-1572B6?logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-vanilla-F7DF1E?logo=javascript&logoColor=black)

[Live demo](#-live-demo) · [Features](#-features) · [Quick start](#-quick-start) · [Deploy](#-deploy-on-infinityfree) · [Structure](#-project-structure)

</div>

---

## 🌐 Live demo

Try it here: **[spillthetea.infinityfree.io](https://spillthetea.infinityfree.io/)**

<!-- Add screenshots to a /screenshots folder, then uncomment:
![Home page](screenshots/home.png)
![Profile page](screenshots/profile.png)
-->

## ✨ Features

| | Feature | Who can use it |
|---|---|---|
| 🖼️ | Meme gallery, newest first | Everyone |
| 🏷️ | Category filters: Relatable, Trending, Random, Dark Humor | Everyone |
| 🔍 | Popup viewer with caption, uploader name and `@username` | Everyone |
| 👍👎 | Like and dislike memes (one reaction per meme, click again to undo) | Logged-in users |
| ⬆️ | Upload memes with a caption and category | Logged-in users |
| 🗑️ | Delete your own memes | Owner only |
| 👤 | Profile page with bio, upload form, your memes and their like/dislike counts | Logged-in users |
| 🌗 | Light and dark theme that remembers your choice | Everyone |
| 📱 | Responsive layout for phones, tablets, laptops and TVs | Everyone |

**Deliberately not included:** no REST API, no AJAX/`fetch()` for data, no comments or share buttons, no admin role. Every page is rendered by PHP and every action is a plain HTML form.

## 🧰 Tech stack

- **Frontend:** HTML, CSS (custom properties for theming), vanilla JavaScript
- **Backend:** PHP with PDO and prepared statements
- **Database:** MySQL / MariaDB
- **Auth:** PHP sessions with `password_hash()` and `password_verify()`

## 🚀 Quick start

<details open>
<summary><b>Run it on your computer</b></summary>

<br>

**You need:** PHP 8+, MySQL or MariaDB (XAMPP, MAMP or Laragon includes both).

1. **Clone the repo**
   ```bash
   git clone https://github.com/pachoriharish/spill-the-tea.git
   cd spill-the-tea
   ```
2. **Create the database.** Import `schema.sql` in phpMyAdmin, or run:
   ```bash
   mysql -u root -p < schema.sql
   ```
3. **Set your database login** at the top of `db.php`:
   ```php
   $dbHost = 'localhost';
   $dbName = 'spillthetea';
   $dbUser = 'root';
   $dbPass = '';
   ```
4. **Make sure `uploads/` is writable.**
5. **Start the site**
   ```bash
   php -S localhost:8000
   ```
   Open <http://localhost:8000>.

**Checklist**

- [ ] Database imported
- [ ] `db.php` updated
- [ ] Site opens at `localhost:8000`
- [ ] Signed up and logged in
- [ ] Uploaded a meme
- [ ] Liked a meme from a second account

</details>

<details>
<summary><b>Already imported an older <code>schema.sql</code> (before likes/dislikes)?</b></summary>

<br>

Import `add_reactions.sql` once. It only adds the `reactions` table.

</details>

## ☁️ Deploy on InfinityFree

<details>
<summary><b>Step-by-step deployment guide</b></summary>

<br>

1. **Upload the files into `htdocs`.** `index.php` must sit directly inside `htdocs`, not inside a sub-folder. Extract the zip first; uploading the `.zip` itself does nothing.
2. **Delete the default page** (`index2.html` or `index.html`) if it's in `htdocs`, because it loads before `index.php`.
3. **Create a database** under *MySQL Databases* in the control panel and copy the host, name and username it shows.
4. **Update `db.php`** with those values:
   ```php
   $dbHost = 'sqlXXX.infinityfree.com';
   $dbName = 'if0_XXXXXXXX_spillthetea';
   $dbUser = 'if0_XXXXXXXX';
   $dbPass = 'your-password';
   ```
5. **Import the tables** in phpMyAdmin. Remove the first two lines of `schema.sql` (`CREATE DATABASE ...` and `USE spillthetea;`) before importing.
6. **Set permissions:** folders 755 (including `uploads/`), PHP files 644.

**Troubleshooting**

| Problem | Fix |
|---|---|
| `403 Forbidden` | `index.php` isn't directly in `htdocs`, or the name isn't exactly lowercase `index.php` |
| Blank page or `500` error | Temporarily add `ini_set('display_errors', 1);` under `<?php` in `db.php` to see the error, then remove it |
| "Could not connect to the database" | Wrong host, name, user or password in `db.php` |
| Uploads fail | Check that `uploads/` exists and is writable (755, or 777 if needed) |

</details>

## 🗂️ Project structure

<details>
<summary><b>Show the file tree</b></summary>

<br>

```
spill-the-tea/
├── index.php          Home: gallery, category filters, popup viewer
├── profile.php        Account info, upload form, your memes (login required)
├── login.php          Handles the login form
├── signup.php         Handles the create-account form
├── logout.php         Ends the session
├── upload.php         Handles meme uploads
├── delete.php         Deletes one of your own memes
├── update_bio.php     Saves your bio
├── react.php          Handles like/dislike clicks (login required)
├── header.php         Shared page top: logo, nav, login/signup window
├── footer.php         Closes the tags that header.php opens
├── db.php             Database connection and helper functions
├── style.css          All styling, light and dark themes
├── script.js          Theme toggle, password show/hide, popup, login tabs
├── schema.sql         Database tables
├── add_reactions.sql  Adds likes/dislikes to an existing database
└── uploads/           Uploaded images are saved here
```

</details>

## 🗄️ Database

<details>
<summary><b>Tables</b></summary>

<br>

| Table | Purpose | Key columns |
|---|---|---|
| `users` | Accounts | `username` (primary key), `password_hash`, `display_name`, `email`, `bio`, `joined` |
| `memes` | Uploaded memes | `id` (UUID), `uploader`, `image_path`, `caption`, `category`, `ts` |
| `reactions` | Likes and dislikes | `meme_id` + `username` (one per user per meme), `value` (`1` like, `-1` dislike) |

Only the image **file name** is stored in the database. The image itself lives in `uploads/`.

</details>

## 🔒 Security notes

<details>
<summary><b>What's protected, and what to do before going live</b></summary>

<br>

**Built in**

- Passwords are hashed with `password_hash()`, never stored as plain text
- Every query uses PDO prepared statements
- All output is escaped with `htmlspecialchars()`
- Uploads are checked by real file type (JPG, PNG, GIF, WebP), limited to 5 MB and renamed randomly
- Delete and like/dislike actions check the logged-in user on the server
- Session id is regenerated at login and signup

**Before you push to GitHub**

- Do **not** commit your real database password. Keep `db.php` with placeholder values in the repo and enter the real ones only on the server.
- Add a `.gitignore` so uploaded images aren't committed:
  ```
  uploads/*
  !uploads/.gitkeep
  ```

</details>

## 🛠️ Built in phases

<details>
<summary><b>Follow the build order</b></summary>

<br>

1. **Database and connection:** `schema.sql`, `db.php`, `uploads/`
2. **Layout, styling and JavaScript:** `header.php`, `footer.php`, `style.css`, `script.js`
3. **Accounts:** `signup.php`, `login.php`, `logout.php`
4. **Home gallery:** `index.php`
5. **Profile, upload, delete:** `profile.php`, `update_bio.php`, `upload.php`, `delete.php`
6. **Polish and testing:** phone, landscape and large-screen layouts; PHP upload limits
7. **Likes and dislikes:** `react.php`, `reactions` table, counts on Home and Profile

</details>

## 🎨 Design

<details>
<summary><b>"Soft & Silly" colour palette</b></summary>

<br>

| Token | Light | Dark | Used for |
|---|---|---|---|
| `--bg` | `#F7F5FF` | `#232132` | Page background |
| `--card` | `#FFFFFF` | `#2c2942` | Cards and tiles |
| `--text` | `#333044` | `#EDEBFF` | Text and button labels |
| `--muted` | `#746f8a` | `#a79fc9` | Secondary text |
| `--primary` | `#BDB2FF` | `#BDB2FF` | Buttons, links, active tabs |
| `--accent` | `#B9FBC0` | `#B9FBC0` | Category badges, active filter, logo highlight |
| `--border` | `#ECE9FB` | `#3b375a` | Borders |

</details>

## 🗺️ Ideas for later

- [ ] Meme search by caption
- [ ] Edit captions on your own memes
- [ ] Pagination or "load more" for large galleries
- [ ] Report button for inappropriate memes
- [ ] A visible footer

## 🤝 Contributing

Fork the repo, create a branch, make your change and open a pull request. Please keep to the plain PHP + forms approach (no frameworks or API calls) so the project stays simple to learn from.


---

<div align="center">

Made with 🍵 and too many memes by **Harish Pachori**

</div>
