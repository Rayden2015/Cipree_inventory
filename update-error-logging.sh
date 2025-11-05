#!/bin/bash
# Script to help bulk update error logging in controllers

CONTROLLERS_DIR="app/Http/Controllers"

echo "=== Adding LogsErrors trait to controllers ==="
echo

# List of controllers to update (top priority ones)
CONTROLLERS=(
    "UserController"
    "EnduserController"
    "InventoryController"
    "StoreRequestController"
    "StockPurchaseRequestController"
    "AuthoriserController"
    "DashboardNavigationController"
    "PurchaseController"
    "OrderController"
    "PartsController"
    "SupplierController"
    "LocationController"
    "ItemController"
    "SiteController"
    "CompanyController"
    "CategoryController"
    "ReviewController"
    "SectionController"
    "DepartmentController"
    "MyAccountController"
    "NotificationController"
    "TotalTaxController"
    "LevyController"
    "SMSController"
    "HomeController"
)

for controller in "${CONTROLLERS[@]}"; do
    file="$CONTROLLERS_DIR/$controller.php"
    if [ -f "$file" ]; then
        # Check if already has trait
        if ! grep -q "use LogsErrors;" "$file"; then
            echo "✓ $controller needs updating"
        else
            echo "  $controller already updated"
        fi
    else
        echo "  $controller not found"
    fi
done

echo
echo "=== Count error handling blocks ===" 
grep -r "floor(time() - 999999999)" "$CONTROLLERS_DIR" --include="*.php" | wc -l

echo
echo "To update manually, use the LogsErrors trait:"
echo "  1. Add 'use App\Traits\LogsErrors;' to imports"
echo "  2. Add 'use LogsErrors;' inside class"
echo "  3. Replace error blocks with: return \$this->handleError(\$e, 'method()');"

