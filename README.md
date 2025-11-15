📦 Bluecart Exporters

A wholesale distribution platform that imports products from Alibaba and supplies them to local retailers at competitive prices.
This repository contains the source code for the Bluecart Exporters website, built to showcase products, manage inquiries, and ensure a responsive experience across all devices.

🚀 Features
🌐 Website Features

Fully responsive design using HTML, CSS, Bootstrap

Product showcase with clean UI

Enquiry/contact form for retailers

Mobile-friendly layout

Simple and secure PHP backend (if applicable)

🧩 Business Features

Importing products from Alibaba at low cost

Bulk distribution to local retailers

Transparent pricing with margin-based profits

Reliable B2B customer handling

🛠️ Tech Stack
Component	Technology
Frontend	HTML, CSS, Bootstrap 5
Backend (optional)	PHP
Database (optional)	MySQL
Version Control	Git & GitHub
Hosting	Any free/static hosting provider (Netlify, GitHub Pages, etc.)
📁 Folder Structure (Example)
Project structure (reflecting your screenshots)
/
├─ admin/
│  ├─ actions/
│  ├─ config/
│  │  ├─ phpmailer/
│  │  │  ├─ Exception.php
│  │  │  ├─ PHPMailer.php
│  │  │  ├─ SMTP.php
│  │  ├─ admin_utils.php
│  │  ├─ auth.php
│  │  ├─ config.php
│  │  ├─ db_connect.php
│  │  ├─ email_config.php
│  │  └─ email.php
│  ├─ controllers/
│  │  ├─ CategoryController.php
│  │  └─ ProductController.php
│  ├─ includes/
│  │  ├─ auth_check.php
│  │  ├─ Controller.php
│  │  ├─ header.php
│  │  └─ sidebar.php
│  └─ views/
│     ├─ categories/
│     │  └─ index.php
│     └─ products/
│        ├─ edit.php
│        ├─ index.php
│        ├─ orders.php
│        └─ ... (inventory.php, settings.php, etc.)
├─ app/                       # Frontend (pages as .tsx)
│  ├─ about/page.tsx
│  ├─ cart/page.tsx
│  ├─ contact/page.tsx
│  ├─ dashboard/page.tsx
│  ├─ login/page.tsx
│  └─ products/page.tsx
├─ components/                # Shared UI components (inferred)
├─ config/
├─ database/
├─ includes/
├─ lib/
├─ public/
├─ styles/
├─ views/
├─ .gitignore
├─ package.json
├─ pnpm-lock.yaml
├─ postcss.config.mjs
├─ next.config.mjs
├─ tailwind.config.ts
├─ tsconfig.json
├─ schema.sql                 # DB schema to import
├─ index.php
├─ products.php
├─ cart.php
├─ checkout.php
├─ register.php
├─ login.php
├─ test_email.php
└─ README.md

Getting started — Local development
Prerequisites

PHP (>=7.4 recommended) and composer if used.

MySQL / MariaDB (or XAMPP / LAMP stack).

Node.js (>=16) and npm or pnpm (pnpm inferred by lockfile).

A browser.

1) Setup database

Create a database (e.g., bluecart).

Import schema.sql via phpMyAdmin or CLI:

# CLI example
mysql -u root -p bluecart < schema.sql


Update DB credentials:

Edit /admin/config/config.php or db_connect.php (or wherever DB constants are defined) and set:

DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=bluecart


(If your project uses .env, create/update it accordingly.)

2) Run PHP backend (admin + PHP pages)

Option A — XAMPP (recommended for Windows):

Place the project folder into htdocs (e.g., C:\xampp\htdocs\bluecart).

Start Apache & MySQL via XAMPP Control Panel.

Visit http://localhost/bluecart/ or http://localhost/bluecart/admin/.

Option B — Built-in PHP server (for quick testing):

# from project root
php -S localhost:8000
# open http://localhost:8000


Note: some routing or .htaccess rules used in production may not work on php -S.

3) Frontend (if using the /app Next.js front)

Install dependencies:

# if using pnpm
pnpm install

# or npm
npm install


Run dev server:

pnpm dev
# or
npm run dev


Open http://localhost:3000 (or the port shown).

If the /app folder is not Next.js, look at package.json scripts and run the appropriate command (e.g., npm run start).

Environment / Mail configuration

Mail config files seen in /admin/config and phpmailer/. Update mail settings for sending notifications:

Typical variables to configure:

MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_FROM=info@bluecartexporters.com
MAIL_FROM_NAME="Bluecart Exporters"
⚙️ How to Run Locally
# Clone the repo
git clone https://github.com/your-username/Bluecart-Exporters.git

# Go inside project folder
cd Bluecart-Exporters

# If using PHP for forms (optional)
php -S localhost:8000


Open the site at:
👉 http://localhost:8000

or
👉 Open index.html directly in your browser

📌 Future Improvements

Add admin dashboard for managing products

Add retailer login system

Add search & filter

SMS/Email enquiry automation

Inventory management module

🧑‍💻 Developers

Chaitanyasinh Vipulsinh Chavda
Founder — Bluecart Exporters
Skills: C++, Web Dev, MySQL, Oracle, PHP, Linux, Bootstrap

📞 Contact

For business inquiries:
📧 chavdachaitanyasinh@gmail.com

📍 Surat, Gujarat, India
