#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Laravel Application Test Runner${NC}"
echo -e "${BLUE}========================================${NC}\n"

# Get the script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Change to backend directory
cd "$SCRIPT_DIR" || exit 1

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  .env file not found. Creating from .env.example...${NC}"
    cp .env.example .env
    php artisan key:generate
fi

# Function to run tests
run_tests() {
    local test_type=$1
    local test_path=$2

    echo -e "${YELLOW}Running ${test_type}...${NC}\n"

    if php artisan test "$test_path" -v; then
        echo -e "\n${GREEN}✓ ${test_type} passed!${NC}\n"
        return 0
    else
        echo -e "\n${RED}✗ ${test_type} failed!${NC}\n"
        return 1
    fi
}

# Track test results
TESTS_PASSED=0
TESTS_FAILED=0

# Run all Feature Tests
if run_tests "Feature Tests" "tests/Feature"; then
    ((TESTS_PASSED++))
else
    ((TESTS_FAILED++))
fi

# Run all Unit Tests
if run_tests "Unit Tests" "tests/Unit"; then
    ((TESTS_PASSED++))
else
    ((TESTS_FAILED++))
fi

# Summary
echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Test Summary${NC}"
echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}Passed: $TESTS_PASSED${NC}"
echo -e "${RED}Failed: $TESTS_FAILED${NC}"

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "\n${GREEN}✓ All tests passed!${NC}\n"
    exit 0
else
    echo -e "\n${RED}✗ Some tests failed!${NC}\n"
    exit 1
fi
