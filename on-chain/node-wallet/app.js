import express,{json} from "express"
import router from "./route/index.js"
import cors from "cors"
const app = express()
const PORT = 3000

app.use(cors({
    "origin": "*",
    "methods": "POST"
}))
app.use(json())
app.use(router)

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`)
})