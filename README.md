# Resume CRUD Lab: INSERT / UPDATE / DELETE

Continue from the previous PHP/MySQL Resume Lab. Student target: manage the `skill` table.

## Setup
1. Use a separate practice database. Import `database/setup.sql` with phpMyAdmin. **Caution: this file DROPS existing resume_lab tables and resets their data.** Do not import into a database with important data.
2. Start Apache + MySQL, place this folder inside your local web root, and visit `http://localhost/<folder>/skills.php`.
3. Configure `config/db.php` for your environment. Defaults `localhost/root/empty password` are for local labs only. For Angsila, obtain real database settings from the administrator and keep credentials out of Git.
4. Read the files: `skills.php`, `skill_create.php`, `skill_edit.php`, `skill_delete.php`.

## Student activities
- Task 1 (INSERT): `skill_create.php` TODO 1, save a new skill, confirm the row appears.
- Task 2 (SELECT + UPDATE): `skill_edit.php` TODO 2a/2b, change level and verify the new value.
- Task 3 (DELETE): `skill_delete.php` TODO 3, delete one practice record and verify it disappears.
- Task 4: explain why UPDATE and DELETE require WHERE. Demonstrate parameter binding.
- Task 5: Git workflow: create feature branch, commit SQL completion, push to GitHub (NO secrets), submit repository URL and screenshots.

## SQL mapping hints
- INSERT: `INSERT INTO ... (...) VALUES (?, ?, ?)`; bind order: `profile_id`, `skill_name`, `skill_level`.
- SELECT: filter using `id = ? AND profile_id = ?`.
- UPDATE: `SET skill_name = ?, skill_level = ?` with both WHERE conditions.
- DELETE: do not omit `WHERE id = ? AND profile_id = ?`.

## Security / scope
The CRUD pages use prepared statements, validation, output escaping, POST for changes, and CSRF tokens. They target the first demo profile only. This is an **unauthenticated classroom example**, not suitable for public deployment on Angsila without sign-in, authorization and production hardening. Prefer local-only access for exercises.

## Grading (100 points)
INSERT 25, SELECT+UPDATE 30, DELETE 25, screenshots/testing 10, Git commits 10.

**Starter:** fill all TODO SQL queries before submitting.
