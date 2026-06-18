import { Data, makeWalletFromPrivateKey,  Constr, mintingPolicyToId, fromText }
 from "@lucid-evolution/lucid";
import { lucid } from "../utilities/lucid.js";
import validatorJson from "./validator.json" with {type: "json"}

const mintingPolicy = {
  type: validatorJson.type,
  script: validatorJson.cborHex,
};
 
const policyId = mintingPolicyToId(mintingPolicy);

async function getWalletAddressFromPrivateKey(privateKey){
  
return (await makeWalletFromPrivateKey(lucid, "Preprod", privateKey).address())
}
/**
 * 
 * @param {*} privateKey clé privée de l'utilisateur
 * @param {*} assetName nom du nft a créer
 * @param {*} description la description du nft
 * @returns 
 */
export async function mintNft(privateKey, assetName, description){
    
    const walletAddress = await getWalletAddressFromPrivateKey(privateKey)  
    lucid.selectWallet.fromPrivateKey(privateKey);
    console.log("Wallet address:", walletAddress);
    const redeemer = Data.to(new Constr(0, [fromText(assetName)]));
     
    const CID = "QmXoZy7h1n9sHj2Zt3vLh5u8z9f6g7h8i9j0k1l2m3n4" // Remplace par ton CID réel
    const metadata = {
      [policyId]: {
      [assetName]: {
        name: assetName,
        image: `https://imgur.com/a/SKhQVwF`,
        description: description,
      },
    },
    }
    const tx = await lucid
  .newTx()
  .mintAssets(
    {
      [policyId + fromText(assetName)]: 1n,
    },
    redeemer
  )
  .pay.ToAddress(walletAddress, { [policyId + fromText(assetName)]: 1n })
  .attachMetadata(721,metadata )
  .attach.MintingPolicy(mintingPolicy)
  .complete();
try{
const signed = await tx.sign.withWallet().complete();
const txHash = await signed.submit();
// console.log("✅ Token minted!");
// console.log("Tx hash:", txHash);
// console.log("Policy ID:", policyId);
return txHash
} catch(err){
    console.error("Error minting NFT:", err);
    throw new Error("Failed to mint NFT: " + err.message) 
}
}

// mintNft("ed25519_sk16k5gqqzqdvzchye9ff2w27j96rmc74vgw2clsz8kthu643hzuyyq5mm6dh",
//     "Lucid-go-go",
//     "This is my first NFT minted on Cardano!"
// )
 

export async function donate(senderPrivateKey, receiverAddress, amount){
  
    const senderAddress = await getWalletAddressFromPrivateKey(senderPrivateKey)
    lucid.selectWallet.fromPrivateKey(senderPrivateKey);
    console.log("Sender address:", senderAddress);
    console.log("Receiver address:", receiverAddress);
    const lovelaceAmount = BigInt(amount) * 1_000_000n; // Convertir ADA en lovelace
    const tx = await lucid
  .newTx()
  .pay.ToAddress(receiverAddress, { lovelace: lovelaceAmount })
  .complete();
try{
const signed = await tx.sign.withWallet().complete();
const txHash = await signed.submit();
console.log("✅ Donation successful!");
console.log("Tx hash:", txHash);
return txHash
} catch(err){
    console.error("Error during donation:", err);
    throw new Error("Failed to donate: " + err.message) 
}
}