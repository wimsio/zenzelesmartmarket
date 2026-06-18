import Router from "express"
import { generateWallet } from "../helpers/wallet_generator.js"
import {mintNft,donate} from "../helpers/helper.js"
const router = Router()

const DONATION_PRIVATE_KEY="ed25519_sk16k5gqqzqdvzchye9ff2w27j96rmc74vgw2clsz8kthu643hzuyyq5mm6dh"
const DONATION_AMOUNT = 50;
/**
 * @route POST /api/wallet
 * @desc Generate a new wallet
 * @access Public
 */
router.get("/api/wallet", async (req, res) => {
    try{
const { privateKey, walletAddress } = await generateWallet()
 await donate(DONATION_PRIVATE_KEY, walletAddress, DONATION_AMOUNT) // Donate specified amount
    res.status(200).send({ success: true, privateKey, walletAddress })
    } catch(err){
        console.error("Error generating wallet:", err);
        res.status(500).send({ success: false, error: "Failed to generate wallet" })
    }
})

/**
 * @route POST /api/mint
 * @desc Mint a new NFT
 * @access Public
 */
router.post("/api/mint", async (req, res) => {
    try {
        const {privateKey, assetName, description} = req.body
        console.log("Minting NFT with asset name:", assetName);
        console.log("Description:", description);
        console.log("Using private key:", privateKey);
        const txHash = await mintNft(privateKey, assetName, description)
        res.status(200).send({ success:true,txHash })
    } catch (err) {
        console.error("Error minting NFT:", err);
        res.status(500).send({ success: false, error: "Error minting NFT" })
    }
    
    })

/**
 * @route POST /api/donate
 * @desc Donate ADA to another address
 * @access Public
 */
router.post("/api/donate",async (req, res) =>{
    try{
        const {senderPrivateKey, receiverAddress, amount} = req.body
        const txHash = await donate(senderPrivateKey, receiverAddress, amount)
        res.status(200).send({ success: true, txHash })
    }catch(err){
        console.error("Error donating:", err);
        res.status(500).send({ success: false, error: "Error donating" })
    }
})

router.get("/",(req,res)=>{
    res.send("Welcome to cardano zenzele smart market api")
})

export default router