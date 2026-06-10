# Zenle Cardano API

**Base URL :** `https://zenle-cardano-api.vercel.app`

---

## POST `/wallet`

Génère un nouveau wallet Cardano.

### Body

Aucun.

### Retourne

```js
{
  privateKey,
  walletAddress
}
```

---

## POST `/mint`

Mint un nouveau NFT sur la blockchain Cardano.

### Body (JSON)

| Champ       | Type   | Requis | Description          |
| ----------- | ------ | ------ | -------------------- |
| privateKey  | string | ✅      | Clé privée du wallet |
| assetName   | string | ✅      | Nom du NFT           |
| description | string | ✅      | Description du NFT   |

### Retourne

```js
{
  txHash
}
```

---

## POST `/donate`

Envoie des ADA d'un compte blockchain vers un autre.

### Body (JSON)

| Champ            | Type   | Requis | Description                          |
| ---------------- | ------ | ------ | ------------------------------------ |
| senderPrivateKey | string | ✅      | Clé privée du wallet de l'expéditeur |
| receiverAddress  | string | ✅      | Adresse blockchain du destinataire   |
| amount           | number | ✅      | Montant d'ADA à envoyer              |

### Retourne

```js
{
  txHash
}
```

---

## GET `/`

Vérifie que l'API est en ligne.
