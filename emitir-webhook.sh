#!/bin/bash

payload='{"evento":"novo_usuario","nome":"Leandro Adrian","email":"leandro@example.com"}'
secret='base64:yDhD514NXZvN7+aEYZv/4i5nUwDRfo/7jHsRjz68G60='
signature=$(echo -n "$payload" | openssl dgst -sha256 -hmac "$secret" | sed 's/^.* //')

curl -X POST http://127.0.0.1:8000/api/webhook \
  -H "Content-Type: application/json" \
  -H "X-Signature: $signature" \
  -d "$payload"
