{-# LANGUAGE DataKinds         #-}
{-# LANGUAGE NoImplicitPrelude #-}
{-# LANGUAGE TemplateHaskell   #-}
{-# LANGUAGE ScopedTypeVariables #-}
{-# LANGUAGE MultiParamTypeClasses #-}
{-# LANGUAGE NumericUnderscores #-}
{-# LANGUAGE OverloadedStrings  #-}


module Main where

import Plutus.V2.Ledger.Api      (
                                   Value, ScriptContext(scriptContextTxInfo),
                                   TxInfo(txInfoMint),
                                   BuiltinData, mkMintingPolicyScript, TokenName(..))
import Plutus.V1.Ledger.Value    ( valueOf )

import Plutus.V2.Ledger.Contexts (ownCurrencySymbol )
import PlutusTx                  ( compile, unstableMakeIsData,
                                   liftCode, applyCode, makeLift, CompiledCode )
import PlutusTx
import PlutusTx.Prelude          ( Bool(False), Integer, (.), negate,
                                   traceIfFalse, Eq((==)),check)
import qualified Prelude         ( Show, IO)
import           Utilities       (wrapPolicy, writeCodeToFile)

-- data MintingDatum= MintingDatum {
--       tokensNumber:: Integer,
--       tokenName:: TokenName
-- }
-- PlutusTx.makeIsDataIndexed
--   ''MintingDatum [('MintingDatum,0)]

data MintingRedeemer = Mint TokenName | Burn TokenName
PlutusTx.makeIsDataIndexed
  ''MintingRedeemer [('Mint,0),
      ('Burn,1)]



{-# INLINEABLE mkPolicy #-}
mkPolicy::MintingRedeemer -> ScriptContext -> Bool
mkPolicy r ctx = case r of
    Mint tokName -> traceIfFalse "Minting failed" (mint tokName  ctx)
    Burn tokName  -> traceIfFalse "Burning failed" (burn tokName ctx)

{-# INLINEABLE mint #-}
mint:: TokenName  -> ScriptContext -> Bool
mint tokName  context =
   let minted = txInfoMint (scriptContextTxInfo context)
    in valueOf minted (ownCurrencySymbol context) tokName == 1


{-# INLINEABLE burn #-}
burn::TokenName  -> ScriptContext -> Bool
burn tokName  context =
   let burned = txInfoMint (scriptContextTxInfo context)
    in valueOf burned (ownCurrencySymbol context) tokName == negate 1


{-# INLINEABLE mintingUntypedValidator #-}
mintingUntypedValidator :: BuiltinData -> BuiltinData -> BuiltinData -> ()
mintingUntypedValidator _ redeemer ctx =
  -- check retourne ()
    check
    (mkPolicy
        (PlutusTx.unsafeFromBuiltinData redeemer)
        (PlutusTx.unsafeFromBuiltinData ctx)
    )

policyCodeLucid :: CompiledCode (BuiltinData -> BuiltinData -> BuiltinData -> ())
policyCodeLucid = $$( compile [|| mintingUntypedValidator ||])

saveMintingCode :: Prelude.IO ()
saveMintingCode = writeCodeToFile "assets/minting.plutus" policyCodeLucid

main:: Prelude.IO ()
main = saveMintingCode
