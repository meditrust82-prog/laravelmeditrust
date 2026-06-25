#!/usr/bin/env bash
set -euo pipefail

BASE=${BASE:-http://localhost:8000}

echo "1) Login (admin)"
curl -s -X POST "$BASE/api/v1/auth/login" -H "Content-Type: application/json" -d '{"email":"admin@example.com","password":"password"}' | jq .

echo "\n2) List products"
curl -s "$BASE/api/v1/products" | jq .

echo "\n3) Get homepage"
curl -s "$BASE/api/v1/homepage" | jq .

echo "\n4) GBP status (expected 401 without token)"
curl -s "$BASE/api/v1/gbp/status" | jq .

echo "\nSmoke tests completed. Inspect above responses for correctness."
