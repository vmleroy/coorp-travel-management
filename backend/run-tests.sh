#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Get the script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Change to backend directory
cd "$SCRIPT_DIR" || exit 1

# Parse command line arguments
TEST_TYPE="all"
COVERAGE=false
FILTER=""
VERBOSE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -u|--unit)
            TEST_TYPE="unit"
            shift
            ;;
        -f|--feature)
            TEST_TYPE="feature"
            shift
            ;;
        -c|--coverage)
            COVERAGE=true
            shift
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        --filter)
            FILTER="$2"
            shift 2
            ;;
        -h|--help)
            echo -e "${BLUE}Laravel Application Test Runner${NC}"
            echo ""
            echo "Usage: ./run-tests.sh [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  -u, --unit              Run only unit tests"
            echo "  -f, --feature           Run only feature tests"
            echo "  -c, --coverage          Run tests with coverage report"
            echo "  -v, --verbose           Verbose output"
            echo "  --filter <pattern>      Filter tests by name pattern"
            echo "  -h, --help              Show this help message"
            echo ""
            echo "Examples:"
            echo "  ./run-tests.sh                      # Run all tests"
            echo "  ./run-tests.sh -u                   # Run only unit tests"
            echo "  ./run-tests.sh -f                   # Run only feature tests"
            echo "  ./run-tests.sh -c                   # Run all tests with coverage"
            echo "  ./run-tests.sh --filter UserRole    # Run tests matching 'UserRole'"
            echo "  ./run-tests.sh -u -v                # Run unit tests with verbose output"
            exit 0
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            echo "Use -h or --help for usage information"
            exit 1
            ;;
    esac
done

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Laravel Application Test Runner${NC}"
echo -e "${BLUE}========================================${NC}\n"

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  .env file not found. Creating from .env.example...${NC}"
    cp .env.example .env
    php artisan key:generate
fi

# Build the test command
TEST_CMD="./vendor/bin/pest"

# Add test type
case $TEST_TYPE in
    unit)
        TEST_CMD="$TEST_CMD tests/Unit"
        echo -e "${CYAN}Running Unit Tests...${NC}\n"
        ;;
    feature)
        TEST_CMD="$TEST_CMD tests/Feature"
        echo -e "${CYAN}Running Feature Tests...${NC}\n"
        ;;
    all)
        echo -e "${CYAN}Running All Tests...${NC}\n"
        ;;
esac

# Add coverage option
if [ "$COVERAGE" = true ]; then
    TEST_CMD="$TEST_CMD --coverage"
    echo -e "${YELLOW}Coverage report will be generated${NC}\n"
else
    TEST_CMD="$TEST_CMD --no-coverage"
fi

# Add filter option
if [ -n "$FILTER" ]; then
    TEST_CMD="$TEST_CMD --filter=$FILTER"
    echo -e "${YELLOW}Filtering tests by: $FILTER${NC}\n"
fi

# Add verbose option
if [ "$VERBOSE" = true ]; then
    TEST_CMD="$TEST_CMD -v"
fi

# Run the tests
echo -e "${BLUE}Command: $TEST_CMD${NC}\n"
echo -e "${BLUE}----------------------------------------${NC}\n"

if eval $TEST_CMD; then
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${GREEN}✓ All tests passed!${NC}"
    echo -e "${BLUE}========================================${NC}\n"
    exit 0
else
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${RED}✗ Some tests failed!${NC}"
    echo -e "${BLUE}========================================${NC}\n"
    exit 1
fi
