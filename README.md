<p align="center">
  <img src="https://symfony.com/logos/symfony_black_03.png" width="80" alt="Symfony Logo" />
  &nbsp;&nbsp;&nbsp;
  <img src="https://vuejs.org/images/logo.png" width="80" alt="Vue.js Logo" />
  &nbsp;&nbsp;&nbsp;
  <img src="https://upload.wikimedia.org/wikipedia/commons/d/d5/Tailwind_CSS_Logo.svg" width="100" alt="Tailwind CSS Logo" />
</p>
<p align="center">
  <img src="https://img.shields.io/badge/Symfony-6.4.22-brightgreen" alt="Symfony" />
  &nbsp;
  <img src="https://img.shields.io/badge/Vue.js-3.3.0-important" alt="Vue.js" />
  &nbsp;
  <img src="https://img.shields.io/badge/Tailwind-3-blueviolet" alt="Tailwind" />
  &nbsp;
  <img src="https://img.shields.io/badge/PHP-8-informational" alt="PHP" />
</p>


---

# 🎵 Track Manager App

A modern full-stack music track manager built with **Symfony 6** and **Vue.js 3**.

> Developed with ❤️ by **Marie**.

---

## ⚙️ Tech Stack

* **Backend:** Symfony 6.4.22 · PHP 8.3.6 · Doctrine ORM · RESTful API
* **Frontend:** Vue.js 3.3.0 · Vite 4 · Tailwind CSS
* **UX:** SweetAlert2 · Axios
* **Database:** MySQL

---

## 📦 Symfony Backend Setup

### 🛠 Installation

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Configure environment:

   * Copy `.env` to `.env.local`
   * Set your database credentials

3. Run database setup:

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. Start the dev server:

   ```bash
   symfony serve
   ```

> Access the backend at `http://127.0.0.1:8000`

---

## 🔌 REST API Endpoints

| Method | Endpoint           | Description              |
| ------ | ------------------ | ------------------------ |
| GET    | `/api/tracks`      | Fetch all tracks         |
| POST   | `/api/tracks`      | Create a new track       |
| PUT    | `/api/tracks/{id}` | Update an existing track |

---

## 🖼️ Vue.js Frontend Setup

### 🛠 Installation

1. Navigate to the frontend folder:

   ```bash
   cd frontend
   ```

2. Install dependencies:

   ```bash
   npm install
   ```

3. Start the dev server:

   ```bash
   npm run dev
   ```

> Make sure the Symfony backend is running in parallel.

---

## ✨ Features

* 🎧 Track list display with edit and create form
* 🚀 Fast bundling with Vite
* 🎨 Responsive design with Tailwind CSS
* 🔔 User-friendly alerts via SweetAlert2

---

## 🧑‍💻 Author

**Marie** — *Built for a coding challenge*

