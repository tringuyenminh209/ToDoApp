#!/bin/bash

# Docker Logs Script for To-Do AI App
# This script shows logs from Docker containers

set -e

# Default to showing all logs
SERVICE=${1:-""}

echo "📋 Showing Docker logs for To-Do AI App..."

if [ -z "$SERVICE" ]; then
    echo "📊 All services logs (press Ctrl+C to exit):"
    docker-compose logs -f
else
    echo "📊 $SERVICE service logs (press Ctrl+C to exit):"
    docker-compose logs -f "$SERVICE"
fi
