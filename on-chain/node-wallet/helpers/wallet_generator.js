import {lucid} from "../utilities/lucid.js";
import { Lucid, generatePrivateKey, makeWalletFromPrivateKey } from "@lucid-evolution/lucid";
/**
 * 
 * @notice Cette fonction permet de generer un portefeuille crypto 
 * @returns privateKey correspondant à la clé privée
 *          walletAddress correspondant à l'adresse blockchain dérivée de la clé privée
 */
export async function generateWallet(){

const privateKey = generatePrivateKey();
const wallet = await makeWalletFromPrivateKey(lucid, "Preprod", privateKey);
const walletAddress = await wallet.address();
// console.log("Wallet address:", walletAddress, " ", {privateKey});

return {privateKey, walletAddress}
}

// generateWallet()

