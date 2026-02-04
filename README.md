# first_react_js

Minimal React starter — learning project / small admin-panel proof-of-concept.

## Features

* Starter React app (Vite recommended)
* Simple state demo in `src/App.jsx`
* Fast dev server, instant HMR
* Clean folder structure to extend into an admin panel

## Requirements

* Node.js **16+**
* npm (or pnpm/yarn)

## Quick start (Vite — recommended)

```bash
git clone <your-repo-url>
cd first_react_js
npm install
npm run dev
# Open: http://localhost:5173
```

## Quick start (Create React App — fallback)

```bash
git clone <your-repo-url>
cd first_react_js
npm install
npm start
# Open: http://localhost:3000
```

## Available scripts

* `npm run dev` — start Vite dev server
* `npm start` — start CRA dev server (if project was created with CRA)
* `npm run build` — production build
* `npm run preview` — preview the Vite build locally

## Where to edit

* `src/App.jsx` — main component (your playground)
* `src/main.jsx` (or `src/index.js`) — app bootstrap / where React mounts
* `index.html` (root HTML)
* `src/assets/` — static assets (logo, images, etc.)
* `src/styles` or `src/App.css` — styling

## Notes & best practices

* This repo is for learning and iteration. Keep commits small and descriptive.
* Prefer Vite for new React projects — faster installs and cleaner deps.
* Keep backend (Laravel) separated; communicate via API endpoints when integrating.
* Add `.env` for environment-specific variables (do **not** commit secrets).

## GitHub publish (simple)

```bash
git add .
git commit -m "Initial React starter"
git branch -M main
git remote add origin <your-repo-url>
git push -u origin main
```

## License

MIT — modify as you like.

## Author

Er. Gokul Subedi — Day 1: React. Long game: Robotics.
