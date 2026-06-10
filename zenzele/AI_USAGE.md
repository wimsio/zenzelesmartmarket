# AI_USAGE.md — Zenzele Smart Market Developer Contest 2026

## Required Documentation of AI Usage

As per contest rules, this file documents all AI assistance used during development.

---

## Model Used
- **Model:** Claude Sonnet 4.5 / Claude Sonnet (Anthropic)
- **Date Used:** May 2026
- **Access Method:** Claude.ai chat interface

---

## What AI Helped With

### 1. Project Structure & Architecture
**Prompt used:**
> "Help me plan a web-based entrepreneur marketplace on Cardano with HTML/CSS/JS frontend, PHP/MySQL backend, multi-language support, NFT minting, audio pitch, and donation features."

**Output:** Directory structure, file list, component breakdown.
**What was accepted:** Overall structure, separation of concerns.
**What was modified:** Tech choices adjusted to match contest spec exactly.

---

### 2. Frontend HTML/CSS
**Prompt used:**
> "Create a homepage for Zenzele Smart Market with hero section, features grid, navbar with language switcher, and contest banner. Use purple/gold color scheme."

**Output:** Full HTML and CSS with CSS variables, responsive grid, animations.
**What was accepted:** Layout, color system, typography, card components.
**What was modified:** Fonts, spacing, demo profile card adjusted manually.

---

### 3. Multi-language (i18n) System
**Prompt used:**
> "Build a JavaScript translation system for 6 languages: English, isiZulu, isiXhosa, Afrikaans, Swahili, Sesotho. Use data-i18n attributes and localStorage."

**Output:** Full `i18n.js` with translations for all 6 languages.
**What was accepted:** Translation structure, data-attribute approach, localStorage persistence.
**What was modified:** Some isiZulu and Sesotho translations were reviewed and corrected by native speakers (TODO: get community review).

---

### 4. JavaScript Features (main.js)
**Prompt used:**
> "Write JavaScript helpers for: localStorage-based profile DB, NFT minting simulator, donation simulator, audio recorder with MediaRecorder API, social sharing (WhatsApp, Facebook, X, Telegram), like/follow toggles, and toast notifications."

**Output:** `main.js` with all above helpers.
**What was accepted:** All functions, used as-is with minor edits.
**What was modified:** Donation simulation updated to include Cardano testnet branding.

---

### 5. Page Components (Register, Login, Explore, Profile, Dashboard)
**Prompt used:**
> "Create HTML pages for: registration form, login with demo mode, entrepreneur explorer with search/filter, profile view with donate/like/follow/share, and a dashboard with audio recorder, NFT minter, and edit profile."

**Output:** All 5 pages.
**What was accepted:** Full structure and functionality.
**What was modified:** Form validation logic manually strengthened; dashboard layout refined.

---

## What Was NOT AI-Generated
- Real Cardano smart contract integration (Haskell/Plutus) — to be done manually
- Real Lucid off-chain transaction code — to be done manually
- PHP/MySQL backend (database schema, API routes) — to be done manually
- IPFS image/audio upload integration — to be done manually
- Real wallet connection (CowryWallet) — to be done manually
- Community translation reviews — to be done by native speakers
- Unit tests — to be written manually

---

## Reproducibility

To reproduce AI-generated sections, use the prompts above with Claude Sonnet or GPT-4 class models. Results will vary but the architecture and approach should be consistent.

All AI outputs were reviewed, tested, and modified before inclusion. AI was used as a tool to accelerate development — not as a replacement for developer judgment.

---

## Transparency Statement

> "We believe AI should accelerate human creativity, not replace it. Every AI-generated component in this repo was reviewed, tested, and adapted by human developers. We used AI to move faster, not to skip understanding."

---

*Zenzele Smart Market — Be Self-Reliant. Build Your Future.*
