const SHA256 = require("crypto-js/sha256")
const { v4: uuidv4 } = require("uuid")
const fs = require("fs")
const path = require("path")

class Block {
  constructor(timestamp, data, previousHash = "") {
    this.index = 0
    this.timestamp = timestamp
    this.data = data
    this.previousHash = previousHash
    this.hash = this.calculateHash()
    this.nonce = 0
  }

  calculateHash() {
    return SHA256(this.index + this.previousHash + this.timestamp + JSON.stringify(this.data) + this.nonce).toString()
  }

  mineBlock(difficulty) {
    while (this.hash.substring(0, difficulty) !== Array(difficulty + 1).join("0")) {
      this.nonce++
      this.hash = this.calculateHash()
    }
    console.log("Block miné: " + this.hash)
  }
}

class Blockchain {
  constructor() {
    this.chain = [this.createGenesisBlock()]
    this.difficulty = 2
    this.pendingTransactions = []
    this.miningReward = 100
    this.loadChain()
  }

  createGenesisBlock() {
    return new Block(Date.now(), { message: "Bloc Genesis de ImmoChain" }, "0")
  }

  getLatestBlock() {
    return this.chain[this.chain.length - 1]
  }

  minePendingTransactions(miningRewardAddress) {
    const rewardTx = {
      id: uuidv4(),
      type: "reward",
      from: "system",
      to: miningRewardAddress,
      amount: this.miningReward,
      timestamp: Date.now(),
    }
    this.pendingTransactions.push(rewardTx)

    const block = new Block(Date.now(), this.pendingTransactions, this.getLatestBlock().hash)
    block.index = this.chain.length
    block.mineBlock(this.difficulty)

    console.log("Block miné avec succès!")
    this.chain.push(block)
    this.pendingTransactions = []
    this.saveChain()

    return block
  }

  registerProperty(propertyData) {
    const transaction = {
      id: uuidv4(),
      type: "property",
      data: propertyData,
      timestamp: Date.now(),
    }
    this.pendingTransactions.push(transaction)
    return transaction
  }

  registerReservation(reservationData) {
    const transaction = {
      id: uuidv4(),
      type: "reservation",
      data: reservationData,
      timestamp: Date.now(),
    }
    this.pendingTransactions.push(transaction)
    return transaction
  }

  getProperty(propertyId) {
    for (const block of this.chain) {
      for (const transaction of block.data) {
        if (transaction.type === "property" && transaction.data.id === propertyId) {
          return {
            transaction: transaction,
            blockHash: block.hash,
            timestamp: block.timestamp,
            blockIndex: block.index,
          }
        }
      }
    }
    return null
  }

  getReservation(reservationId) {
    for (const block of this.chain) {
      for (const transaction of block.data) {
        if (transaction.type === "reservation" && transaction.data.id === reservationId) {
          return {
            transaction: transaction,
            blockHash: block.hash,
            timestamp: block.timestamp,
            blockIndex: block.index,
          }
        }
      }
    }
    return null
  }

  getAllProperties() {
    const properties = []
    for (const block of this.chain) {
      for (const transaction of block.data) {
        if (transaction.type === "property") {
          properties.push({
            transaction: transaction,
            blockHash: block.hash,
            timestamp: block.timestamp,
            blockIndex: block.index,
          })
        }
      }
    }
    return properties
  }

  getAllReservations() {
    const reservations = []
    for (const block of this.chain) {
      for (const transaction of block.data) {
        if (transaction.type === "reservation") {
          reservations.push({
            transaction: transaction,
            blockHash: block.hash,
            timestamp: block.timestamp,
            blockIndex: block.index,
          })
        }
      }
    }
    return reservations
  }

  isChainValid() {
    for (let i = 1; i < this.chain.length; i++) {
      const currentBlock = this.chain[i]
      const previousBlock = this.chain[i - 1]

      if (currentBlock.hash !== currentBlock.calculateHash()) {
        return false
      }

      if (currentBlock.previousHash !== previousBlock.hash) {
        return false
      }
    }
    return true
  }

  saveChain() {
    try {
      // Utilisation de chemins absolus pour éviter les problèmes de répertoire
      const dataDir = path.join(__dirname, "data")

      // Créer le répertoire s'il n'existe pas
      if (!fs.existsSync(dataDir)) {
        fs.mkdirSync(dataDir, { recursive: true })
      }

      const blockchainPath = path.join(dataDir, "blockchain.json")
      const pendingPath = path.join(dataDir, "pending.json")

      fs.writeFileSync(blockchainPath, JSON.stringify(this.chain, null, 2))
      fs.writeFileSync(pendingPath, JSON.stringify(this.pendingTransactions, null, 2))
      console.log("Blockchain sauvegardée avec succès")
    } catch (error) {
      console.error("Erreur lors de la sauvegarde de la blockchain:", error)
    }
  }

  loadChain() {
    try {
      const dataDir = path.join(__dirname, "data")
      const blockchainPath = path.join(dataDir, "blockchain.json")
      const pendingPath = path.join(dataDir, "pending.json")

      if (fs.existsSync(blockchainPath)) {
        const chainData = fs.readFileSync(blockchainPath, "utf8")
        this.chain = JSON.parse(chainData)
        console.log("Blockchain chargée avec succès")
      }

      if (fs.existsSync(pendingPath)) {
        const pendingData = fs.readFileSync(pendingPath, "utf8")
        this.pendingTransactions = JSON.parse(pendingData)
        console.log("Transactions en attente chargées avec succès")
      }
    } catch (error) {
      console.error("Erreur lors du chargement de la blockchain:", error)
    }
  }
}

module.exports = { Blockchain, Block }
