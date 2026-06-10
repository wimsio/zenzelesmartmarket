const lucid = require('lucid-cardano'); const fs = require('fs'); const out = Object.keys(lucid).map(k = = lucid['${k}'];`).join('\n'); fs.writeFileSync('js/lucid.js', out); console.log('Done'); 
