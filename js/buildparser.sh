pegjs -o parserTmp.js --format globals  --export-var parser $1
sed -E -f replace.sed parserTmp.js > parser.js
rm parserTmp.js
