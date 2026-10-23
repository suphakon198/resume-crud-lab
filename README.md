# Resume CRUD Lab: INSERT / UPDATE / DELETE + Session Login

Continues the previous PHP/MySQL Resume Lab. Each registered user has their own profile and manages their own rows in the `skill` table (CRUD), protected by PHP session authentication.

- Live demo (Angsila): https://angsila.informatics.buu.ac.th/~68160403/resume-crud-lab/login.php
- Task 4 explanation (WHERE and parameter binding): [TASK4.md](TASK4.md)

## Features
- **Register / Login / Logout** with PHP sessions and `password_hash` / `password_verify`
- **Skill CRUD** (`skills.php`, `skill_create.php`, `skill_edit.php`, `skill_delete.php`) with prepared statements
- **Per-user data**: every SELECT/UPDATE/DELETE on `skill` filters by the `profile_id` stored in the session
- **Resume page** (`index.php`) shows the profile, education, skills and training of the logged-in user

## Project structure
| File | Purpose |
|---|---|
| `auth.php` | Session setup, `require_login()`, CSRF helpers for the login forms |
| `register.php` | Create a `profile` and a `users` row (password stored as a hash) |
| `login.php` | Verify email and password, start the session |
| `logout.php` | POST + CSRF logout, destroy the session |
| `crud_bootstrap.php` | Requires login, DB connection, CSRF and HTML helpers for CRUD pages |
| `skills.php` | List the logged-in user's skills |
| `skill_create.php` | INSERT a skill |
| `skill_edit.php` | SELECT one skill and UPDATE it |
| `skill_delete.php` | DELETE a skill after confirmation |
| `index.php` | Resume page (requires login) |
| `config/db.php` | Database settings (**not committed to Git**) |
| `database/setup.sql` | Database schema and sample data |

## Setup
1. Use a separate practice database. Import `database/setup.sql` with phpMyAdmin. **Caution: this file DROPS existing resume_lab tables and resets their data.** Do not import into a database with important data.
2. Make sure the `users` table exists (`id`, `profile_id`, `email` (unique), `password_hash`) together with `profile`, `education`, `skill` and `training`.
3. Create `config/db.php` for your environment. Defaults `localhost/root/empty password` are for local labs only. On Angsila use the real database settings and **keep credentials out of Git** (`config/db.php` is listed in `.gitignore`).
4. Start Apache + MySQL, place this folder inside your local web root, and visit `http://localhost/<folder>/login.php`. Register an account, log in, then use `skills.php`.

## Student activities
- Task 1 (INSERT): `skill_create.php` TODO 1, save a new skill, confirm the row appears.
- Task 2 (SELECT + UPDATE): `skill_edit.php` TODO 2a/2b, change level and verify the new value.
- Task 3 (DELETE): `skill_delete.php` TODO 3, delete one practice record and verify it disappears.
- Task 4: explain why UPDATE and DELETE require WHERE and demonstrate parameter binding (see `TASK4.md`).
- Task 5: Git workflow: feature branch, commits, push to GitHub (no secrets), submit the repository URL and screenshots.
- Login/Logout: `login.php` TODO 1-5 and `logout.php` TODO 6.

## SQL mapping
- INSERT: `INSERT INTO skill (profile_id, skill_name, skill_level) VALUES (?, ?, ?)`; bind order: `profile_id`, `skill_name`, `skill_level`.
- SELECT: `... WHERE id = ? AND profile_id = ?`.
- UPDATE: `SET skill_name = ?, skill_level = ? WHERE id = ? AND profile_id = ?`.
- DELETE: `DELETE FROM skill WHERE id = ? AND profile_id = ?`. Never omit the WHERE clause.
- Login lookup: `SELECT id, profile_id, password_hash FROM users WHERE email = ? LIMIT 1`.

## Security
- Prepared statements and parameter binding for every query that uses user input
- Output escaping with `htmlspecialchars`
- POST for all changes (create, update, delete, logout) with CSRF tokens
- Passwords stored only as `password_hash`, checked with `password_verify`
- `session_regenerate_id(true)` after login; session cookie is `httponly` and `samesite=Lax`; `use_strict_mode` and `use_only_cookies` enabled
- Ownership check: `profile_id` comes from the session, never from the request, so a user cannot read, edit or delete another user's skills
- Database credentials are kept out of Git

This is still a classroom example. It does not include rate limiting, password reset, email verification, or other production hardening, and it should be served over HTTPS if deployed publicly.

---

# Session และขั้นตอน Login / Logout

## Session คืออะไร
HTTP ไม่จำผู้ใช้ระหว่างคำขอ PHP จึงออก session ID ให้เบราว์เซอร์เก็บเป็นคุกกี้ ส่วนข้อมูลของผู้ใช้ (`user_id`, `profile_id`, `email`) เก็บฝั่งเซิร์ฟเวอร์ใน `$_SESSION` `auth.php` เปิด session ด้วย `use_strict_mode`, `use_only_cookies`, คุกกี้ `httponly` และ `samesite=Lax`

## Register (`register.php`)
ตรวจอีเมลและความยาวรหัสผ่าน (10 ตัวขึ้นไป) เก็บเฉพาะ `password_hash` ในตาราง `users` ไม่เก็บรหัสผ่านจริง และสร้างแถวใหม่ในตาราง `profile` ให้ผู้ใช้แต่ละคน

## Login (`login.php`)
1. ตรวจ CSRF token แล้วตรวจรูปแบบอีเมล
2. ค้นหาผู้ใช้ด้วย prepared statement: `SELECT id, profile_id, password_hash FROM users WHERE email = ? LIMIT 1`
3. เทียบรหัสผ่านด้วย `password_verify($password, $hash)`
4. ถ้าถูก เรียก `session_regenerate_id(true)` เปลี่ยน session ID ใหม่ (ป้องกัน session fixation) แล้วเก็บ `user_id`, `profile_id`, `email` ใน session
5. Redirect ไป `skills.php`

ถ้าอีเมลหรือรหัสผ่านผิด แสดงข้อความเดียวกันว่า "Invalid email or password." เพื่อไม่บอกว่าอีเมลนั้นมีในระบบหรือไม่

## หน้าที่ต้องล็อกอิน
`crud_bootstrap.php` และ `index.php` เรียก `require_login()` ถ้าไม่มี `user_id` และ `profile_id` ใน session จะถูกส่งไป `login.php` ทุกคำสั่ง SELECT/UPDATE/DELETE ของ skill ใช้ `profile_id` จาก session ใน WHERE จึงแก้หรือลบข้อมูลของคนอื่นไม่ได้

## Logout (`logout.php`)
ใช้ POST ร่วมกับ CSRF token เพื่อไม่ให้เว็บอื่นหลอกให้ผู้ใช้ออกจากระบบ แล้วล้าง `$_SESSION`, ตั้งคุกกี้ session ให้หมดอายุ, เรียก `session_destroy()` และ redirect ไป `login.php`

## ผลการทดสอบ
- ยังไม่ล็อกอินเข้า `skills.php` ถูกส่งไปหน้า Login
- รหัสผ่านผิดล็อกอินไม่ผ่าน
- หลัง Logout กด Back/refresh เข้าข้อมูลเดิมไม่ได้
- สองบัญชีไม่เห็นหรือแก้ข้อมูลของกัน (ได้ Record not found)
- ใส่ SQL injection ในช่องอีเมลแล้วล็อกอินไม่ผ่าน

## Grading (100 points)
INSERT 25, SELECT+UPDATE 30, DELETE 25, screenshots/testing 10, Git commits 10.
