#!/bin/bash

echo "🔍 Testing Laravel Permissions..."
echo "=================================="

# Test storage directory permissions
echo "📁 Storage Directory Structure:"
ls -la storage/

echo ""
echo "📁 Storage/app/public permissions:"
ls -la storage/app/public/ 2>/dev/null || echo "❌ storage/app/public doesn't exist"

echo ""
echo "📁 Storage/app/public/depot-maps permissions:"
ls -la storage/app/public/depot-maps/ 2>/dev/null || echo "❌ depot-maps directory doesn't exist"

echo ""
echo "🔗 Storage symlink check:"
ls -la public/storage 2>/dev/null || echo "❌ storage symlink doesn't exist"

echo ""
echo "✏️  Write test to storage/logs:"
echo "test write $(date)" > storage/logs/permission-test.log 2>/dev/null && echo "✅ Can write to logs" || echo "❌ Cannot write to logs"

echo ""
echo "✏️  Write test to storage/app/public/depot-maps:"
mkdir -p storage/app/public/depot-maps 2>/dev/null
echo "test" > storage/app/public/depot-maps/test.txt 2>/dev/null && echo "✅ Can write to depot-maps" || echo "❌ Cannot write to depot-maps"
rm storage/app/public/depot-maps/test.txt 2>/dev/null

echo ""
echo "🌐 Web server can access storage through symlink:"
if [ -L "public/storage" ] && [ -e "public/storage" ]; then
    echo "✅ Storage symlink exists and works"
else
    echo "❌ Storage symlink broken or missing"
fi

echo ""
echo "🔐 Current user and group:"
whoami
id

echo ""
echo "🔐 Storage directory ownership:"
ls -ld storage/
ls -ld storage/app/ 2>/dev/null || echo "storage/app doesn't exist"
ls -ld storage/app/public/ 2>/dev/null || echo "storage/app/public doesn't exist"

echo ""
echo "=================================="
echo "✅ Permission test complete!"