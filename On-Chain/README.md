# On-Chain — Zenzele Smart Market

This folder contains the blockchain connector and smart contract sources used by the project.

Overview
- `blockchain_connexion/` — Node.js microservice that exposes a small HTTP API to interact with wallets and the Cardano blockchain via `lucid`.
- `contract/` — Haskell sources (e.g. `Main.hs`) for the on-chain smart contract (Plutus or other Haskell-based contract).

Requirements
- Node.js 18+ (or >=16 for many lucid versions).
- npm (comes with Node.js).
- For smart contract development: GHC, Cabal or Stack and the Cardano/Plutus toolchain if you plan to build or deploy the contract.

Run the blockchain connector (Node.js)
1. Open a terminal in `blockchain_connexion/`.
2. Install dependencies:

```bash
cd On-Chain/blockchain_connexion
npm install
```

3. Start the server in development:

```bash
npm run dev
# or
node app.js
```

4. The service listens by default on port `3000`. The REST routes are implemented in `route/index.js`.

Notes about the Haskell contract
- The `contract/` folder contains `Main.hs`. Building and deploying a Plutus contract requires the Cardano/Plutus toolchain (GHC with specific compiler flags, plutus-apps or a similar environment). This project does not include an automated contract build script; follow your usual Cardano smart contract toolchain instructions to compile and test the contract.

Integration with Off-chain
- The PHP application (Off-chain) calls the blockchain connector API to perform wallet-related actions (check `public/assets/js/*` and `api/` for integration points).
- Ensure the Node service runs and is reachable from your PHP server (CORS is enabled in `app.js` with origin `*`).

Security notes
- Never expose private keys or store them in plaintext for production. Use secure key management or hardware wallets.
- Lock down CORS and network access in production.

If you want, I can add a small example `curl` request and document the available API endpoints after inspecting `route/index.js`.

Deployed API
- The Node.js connector for this project is deployed to Vercel and available at: https://zenle-cardano-api.vercel.app/
- You can use this hosted endpoint from the Off-chain site or other clients instead of running the local Node server.

API Routes
Below are the routes exposed by the Node connector (also available at the Vercel host above).

- `GET /api/wallet`
	- Description: Generate a new wallet (private key + address). The service also donates a small amount to the new address using an internal donation key.
	- Response (200): `{ success: true, privateKey: string, walletAddress: string }`
	- Example:

	```bash
	curl -X GET https://zenle-cardano-api.vercel.app/api/wallet
	```

- `POST /api/mint`
	- Description: Mint a new NFT using a provided private key and asset metadata.
	- Request JSON body: `{ "privateKey": "<ed25519_sk...>", "assetName": "MyToken", "description": "Description text" }`
	- Response (200): `{ success: true, txHash: string }`
	- Example:

	```bash
	curl -X POST https://zenle-cardano-api.vercel.app/api/mint \
		-H "Content-Type: application/json" \
		-d '{"privateKey":"ed25519_sk...","assetName":"TestNFT","description":"Test mint"}'
	```

- `POST /api/donate`
	- Description: Send ADA from a sender private key to a receiver address. `amount` is in ADA (not lovelace).
	- Request JSON body: `{ "senderPrivateKey": "<ed25519_sk...>", "receiverAddress": "addr_test...", "amount": 10 }
	- Response (200): `{ success: true, txHash: string }`
	- Example:

	```bash
	curl -X POST https://zenle-cardano-api.vercel.app/api/donate \
		-H "Content-Type: application/json" \
		-d '{"senderPrivateKey":"ed25519_sk...","receiverAddress":"addr_test...","amount":5}'
	```

- `GET /`
	- Description: Root health/welcome endpoint returning a simple welcome message.

Notes
- `amount` in `/api/donate` is interpreted as ADA and converted internally to lovelace (1 ADA = 1_000_000 lovelace).
- `/api/mint` expects a valid private key that the lucid instance can use to sign and submit the minting transaction.
- Do not expose real private keys when testing on shared or public environments.


