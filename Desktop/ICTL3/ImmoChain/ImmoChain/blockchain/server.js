const express = require("express")
const bodyParser = require("body-parser")
const cors = require("cors")
const { Blockchain } = require("./blockchain")
const jwt = require("jsonwebtoken")
const fs = require("fs")
const path = require("path")
require("dotenv").config()

// Créer le dossier data s'il n'existe pas
const dataDir = path.join(__dirname, "data")
if (!fs.existsSync(dataDir)) {
  fs.mkdirSync(dataDir, { recursive: true })
}

const app = express()
app.use(bodyParser.json())
app.use(cors())

// Servir les fichiers statiques depuis le dossier public
app.use(express.static(path.join(__dirname, "public")))

// Initialiser la blockchain
const immoChain = new Blockchain()

// Ajouter cette route au début des routes dans server.js
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "public", "index.html"))
})

// Middleware pour vérifier le token JWT
const authenticateToken = (req, res, next) => {
  const authHeader = req.headers["authorization"]
  const token = authHeader && authHeader.split(" ")[1]

  if (token == null) return res.sendStatus(401)

  jwt.verify(token, process.env.JWT_SECRET || "votre_secret_jwt", (err, user) => {
    if (err) return res.sendStatus(403)
    req.user = user
    next()
  })
}

// Route pour l'authentification
app.post("/api/auth", (req, res) => {
  const { username, password } = req.body

  // Vérification simple des identifiants (à remplacer par votre propre logique)
  if (username === "admin" && password === "password") {
    const user = { id: 1, username: username }
    const accessToken = jwt.sign(user, process.env.JWT_SECRET || "votre_secret_jwt", { expiresIn: "1h" })
    res.json({ accessToken })
  } else {
    res.status(401).json({ message: "Identifiants invalides" })
  }
})

// Route pour enregistrer un bien immobilier
app.post("/api/properties", authenticateToken, (req, res) => {
  const propertyData = req.body
  const transaction = immoChain.registerProperty(propertyData)
  res.status(201).json({
    message: "Bien immobilier enregistré avec succès dans la blockchain",
    transaction: transaction,
  })
})

// Route pour enregistrer une réservation
app.post("/api/reservations", authenticateToken, (req, res) => {
  const reservationData = req.body
  const transaction = immoChain.registerReservation(reservationData)
  res.status(201).json({
    message: "Réservation enregistrée avec succès dans la blockchain",
    transaction: transaction,
  })
})

// Route pour miner les transactions en attente
app.post("/api/mine", authenticateToken, (req, res) => {
  const { rewardAddress } = req.body

  if (!rewardAddress) {
    return res.status(400).json({ message: "Adresse de récompense requise" })
  }

  if (immoChain.pendingTransactions.length === 0) {
    return res.status(400).json({ message: "Aucune transaction en attente à miner" })
  }

  const newBlock = immoChain.minePendingTransactions(rewardAddress)

  res.json({
    message: "Bloc miné avec succès",
    block: newBlock,
  })
})

// Route pour obtenir toute la blockchain
app.get("/api/blockchain", (req, res) => {
  res.json(immoChain.chain)
})

// Route pour obtenir les transactions en attente
app.get("/api/pending", (req, res) => {
  res.json(immoChain.pendingTransactions)
})

// Route pour vérifier la validité de la blockchain
app.get("/api/validate", (req, res) => {
  const isValid = immoChain.isChainValid()
  res.json({ valid: isValid })
})

// Route pour obtenir un bien immobilier spécifique
app.get("/api/properties/:id", (req, res) => {
  const propertyId = Number.parseInt(req.params.id)
  const property = immoChain.getProperty(propertyId)

  if (property) {
    res.json(property)
  } else {
    res.status(404).json({ message: "Bien immobilier non trouvé dans la blockchain" })
  }
})

// Route pour obtenir une réservation spécifique
app.get("/api/reservations/:id", (req, res) => {
  const reservationId = Number.parseInt(req.params.id)
  const reservation = immoChain.getReservation(reservationId)

  if (reservation) {
    res.json(reservation)
  } else {
    res.status(404).json({ message: "Réservation non trouvée dans la blockchain" })
  }
})

// Route pour obtenir tous les biens immobiliers
app.get("/api/properties", (req, res) => {
  const properties = immoChain.getAllProperties()
  res.json(properties)
})

// Route pour obtenir toutes les réservations
app.get("/api/reservations", (req, res) => {
  const reservations = immoChain.getAllReservations()
  res.json(reservations)
})

// Route pour l'explorateur de blockchain
app.get("/explorer/block/:index", (req, res) => {
  res.sendFile(path.join(__dirname, "public", "block.html"))
})

// Route pour obtenir une transaction spécifique (API JSON)
app.get("/api/explorer/transaction/:id", (req, res) => {
  const transactionId = req.params.id
  let foundTransaction = null

  // Rechercher la transaction dans tous les blocs
  for (let i = 0; i < immoChain.chain.length; i++) {
    const block = immoChain.chain[i]

    // Vérifier si block.data est un tableau
    if (Array.isArray(block.data)) {
      // Parcourir toutes les transactions du bloc
      for (let j = 0; j < block.data.length; j++) {
        const tx = block.data[j]
        if (tx && tx.id === transactionId) {
          foundTransaction = {
            transaction: tx,
            blockHash: block.hash,
            blockIndex: block.index,
            timestamp: block.timestamp,
          }
          break
        }
      }
    }
    // Si block.data est un objet unique (cas du bloc genesis ou autre)
    else if (block.data && typeof block.data === "object" && block.data.id === transactionId) {
      foundTransaction = {
        transaction: block.data,
        blockHash: block.hash,
        blockIndex: block.index,
        timestamp: block.timestamp,
      }
    }

    if (foundTransaction) break
  }

  if (foundTransaction) {
    res.json(foundTransaction)
  } else {
    res.status(404).json({ message: "Transaction non trouvée" })
  }
})

// Route pour la page HTML de transaction
app.get("/explorer/transaction/:id", (req, res) => {
  const transactionId = req.params.id
  res.sendFile(path.join(__dirname, "public", "transaction.html"))
})

// Démarrer le serveur
const PORT = process.env.PORT || 3000
app.listen(PORT, () => {
  console.log(`Serveur blockchain démarré sur le port ${PORT}`)
})
