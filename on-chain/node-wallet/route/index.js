import Router from "express"
import { generateWallet } from "../helpers/wallet_generator.js"
import {mintNft,donate} from "../helpers/helper.js"
const router = Router()

/**
 * @route POST /api/wallet
 * @desc Generate a new wallet
 * @access Public
 */
router.post("/api/wallet", async (req, res) => {
    try{
const { privateKey, walletAddress } = await generateWallet()
    res.status(200).send({ privateKey, walletAddress })
    } catch(err){
        console.error("Error generating wallet:", err);
        res.status(500).send({ error: "Failed to generate wallet" })
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
        const txHash = await mintNft(privateKey, assetName, description)
        res.status(200).send({ txHash })
    } catch (err) {
        console.error("Error minting NFT:", err);
        res.status(500).send({ error: "Error minting NFT" })
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
        res.status(200).send({ txHash })
    }catch(err){
        console.error("Error donating:", err);
        res.status(500).send({ error: "Error donating" })
    }
})

export default router