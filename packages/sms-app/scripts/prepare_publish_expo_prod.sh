#!/bin/bash

# Change the manifest
sed -i 's/: "MOVES"/: "MOVES Production"/g' app.json
sed -i 's/: "melandas-app"/: "melandas-app-prod"/g' app.json

# Hardcode endpoint to prod
# DEBT: Somehow not hardcode this
sed -i 's/melandas.ilios.id/app.melandas-indonesia.com/g' src/hooks/useApi.ts
