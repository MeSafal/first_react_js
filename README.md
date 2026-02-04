````markdown
# first_react_js

Small, focused React admin-panel prototype — Day 1 of the frontend journey.  
Built with Create React App (CRA). Recommend migrating to Vite for real work.

---

## Quick snapshot
- Single-page React app
- Basic state example in `src/App.jsx`
- Meant for local dev and later connection to a Laravel backend via API

---

## Prerequisites
- Node.js (16+) and npm (or yarn)
- Git (optional)

---

## Setup (current CRA project)
```bash
# clone
git clone <your-repo-url> first_react_js
cd first_react_js

# install deps
npm install

# dev server
npm start

# build for production
npm run build
````

---

## If you prefer Vite (recommended)

```bash
# create fresh Vite project (recommended for speed)
npm create vite@latest my-admin-panel
cd my-admin-panel
npm install
npm run dev
```

*Migration note: Vite is faster, simpler, and has modern defaults.*

---

## Available scripts (CRA)

* `npm start` — start dev server (hot reload)
* `npm run build` — production bundle in `build/`
* `npm test` — run tests (if added)
* `npm run eject` — eject CRA (one-way)

---

## Project structure (key files)

```
first_react_js/
├─ public/
│  └─ index.html
├─ src/
│  ├─ assets/
│  ├─ App.jsx
│  ├─ index.js
│  └─ App.css
├─ package.json
└─ README.md
```

---

## Connect to backend (Laravel API)

1. Add environment variables (create `.env.local` at project root):

```
REACT_APP_API_BASE_URL=http://localhost:8000/api
```

2. Use `fetch` or `axios`:

```js
// example
const res = await fetch(`${process.env.REACT_APP_API_BASE_URL}/users`);
```

3. Handle CORS on Laravel side (`cors` middleware / `laravel-cors` config).

---

## Tips & best practices

* Keep UI presentational components separate from data/container components.
* Use `axios` for API calls + interceptors for auth tokens.
* Move state to Context or a state manager (Redux / Zustand) when app grows.
* Add linting (ESLint + Prettier) before PRs.

---

## Todo (next steps)

* Replace CRA with Vite
* Create basic admin layout (sidebar, topbar, table)
* Add auth flow (token-based) and connect to Laravel
* Add unit tests for critical components

---

## Contributing

Small PRs welcome. Keep changes focused and document behavior.

---

## License

MIT — feel free to reuse and iterate.

---

## Author

Er. Gokul Subedi

```
