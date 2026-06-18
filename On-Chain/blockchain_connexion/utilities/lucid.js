import { Lucid, Blockfrost, generateSeedPhrase } from "@lucid-evolution/lucid";

// Initialize Lucid with a provider
export const lucid = await Lucid(
  new Blockfrost("https://cardano-preprod.blockfrost.io/api/v0",
     "preprodYjRkHfcazNkL0xxG9C2RdUbUoTrG7wip"),
  "Preprod",
);