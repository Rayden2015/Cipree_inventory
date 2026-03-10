# Cipree Inventory Correction & Audit Framework (V4.0) – Analysis & Implementation Proposal

## 1. Current System Analysis

### 1.1 Receiving Flow (GRN Entry)

| Component | Current Implementation |
|-----------|------------------------|
| **Entry point** | `InventoryController::create()` → `store()` |
| **Header** | `inventories` (grn_number, waybill, supplier_id, site_id, tenant_id, user_id, etc.) |
| **Lines** | `inventory_items` (inventory_id, item_id, location_id, **quantity**, unit_cost_exc_vat_gh, amount, site_id, tenant_id, last_updated_by) |
| **Detail copy** | `inventory_item_details` (same fields as line, keyed by inventory_id; used in “history” views) |
| **Stock rollup** | `items.stock_quantity` = SUM(inventory_items.quantity) per item (no status filter) |

- **Who can add GRN**: Permission `add-grn` (Store Officer / Store Assistant type roles).
- **Persistence**: Direct INSERT; no status field. Quantity and price are stored as single values (spreadsheet style, not ledger).

### 1.2 Supplying / Issuing Flow

| Component | Current Implementation |
|-----------|------------------------|
| **Request** | Requester searches items; cart uses `inventory_items.id` and quantity. |
| **Store order** | `sorders` (header) + `sorder_parts` (inventory_id → **inventory_items.id**, quantity, qty_supplied). |
| **Deduction** | `StoreRequestController::store_officer_update()`: for each line, `new_quantity = inventory_items.quantity - sorder_parts.qty_supplied`, then `InventoryItem::updateOrCreate(..., ['quantity' => new_quantity])`. |
| **Rollup** | Same as receiving: `items.stock_quantity` = SUM(inventory_items.quantity). |

- **Who can supply**: Store Officer marks as Supplied and enters qty_supplied; system directly **updates** `inventory_items.quantity` (no additive inverse).
- **Link to GRN line**: `sorder_parts.inventory_id` → `inventory_items.id`, so “issued from this GRN line” = SUM(sorder_parts.qty_supplied) WHERE inventory_id = inventory_items.id.

### 1.3 Correction / Edit Flow (Current – Not Compliant with V4.0)

| Location | Behaviour | V4.0 Compliant? |
|----------|-----------|------------------|
| `InventoryController::update_inventory_item()` | Direct **UPDATE** of quantity, unit_cost_exc_vat_gh, amount, etc. on `inventory_items`. | No (must be additive inverse only). |
| `InventoryController::inventory_history_action()` | **UPDATE** quantity/price on both `inventory_items` and `inventory_item_details`; **DELETE** from `inventory_items`. | No (no DELETE; no overwrite of qty/price). |
| `InventoryController::update_inventory_history()` | **UPDATE** quantity/price on `inventory_item_details` only. | No (ledger and status required). |
| `StoreRequestController::store_officer_update()` | **UPDATE** quantity on `inventory_items` (quantity -= qty_supplied). | No (should create adjustment entries instead of mutating original). |

- There is **no** “Flag for Correction” or Supervisor approval; any user with `edit-grn` can change quantities and prices in place.
- **Audit**: `update_inventory_item` table logs before/after JSON for one code path only; no structured ledger, no reason codes, no source_transaction_id.

### 1.4 Requester View (Search / Availability)

- **Current**: `StoreRequestController::requester_search()` joins `items` + `inventory_items` with `inventory_items.quantity > 0` (no status filter). Requester sees **raw line-level** rows (e.g. multiple rows per item from different GRNs/locations).
- **V4.0**: Requester should see an **aggregated** view: SUM(quantity) WHERE Status IN ('Active','Adjustment') GROUP BY item/location, and only GRNs with Status = 'Active' and current_net_balance > 0 for picking. No voided/negative lines.

### 1.5 Roles and Permissions (Current vs V4.0)

| V4.0 Role | Current Equivalent | Notes |
|-----------|--------------------|--------|
| Stores Officer | `store_officer`, `add-grn`, `edit-grn` | Can currently edit qty/price directly; V4.0: only “Initiate” correction. |
| Requester | Requester flows (no explicit role name) | View must be filtered (status + aggregation). |
| Supervisor (The Boss) | `Super Authoriser` or new role | V4.0: only role with “Write Access” to execute adjustments. |

- No permission today for “execute adjustment” or “approve correction” separately from “edit GRN”.

---

## 2. Gaps Summary vs V4.0

1. **No status on ledger lines**  
   - Missing: `status` (Active / Voided / Adjustment) on the table that represents “one GRN line” (and any adjustment lines).  
   - Currently `inventory_items` has no status; `inventory_item_details` is a duplicate of receipt data, not a full ledger.

2. **Direct mutation of quantity/price**  
   - All corrections are done via UPDATE/DELETE on quantity and unit cost.  
   - V4.0 requires: **immutable ledger**; corrections only via **additive inverse** (new rows), no UPDATE/DELETE on qty/price.

3. **No correction workflow**  
   - No “Flag Error for Correction” by Stores Officer.  
   - No “Request” (intended amount) or Supervisor approval.  
   - No “Void + Mirror + New” (Scenario A) or “Negative Adjustment + shortfall alert” (Scenario B).

4. **No net balance / issued tracking per line**  
   - “Already issued” from a GRN line = SUM(sorder_parts.qty_supplied) WHERE inventory_id = inventory_items.id. This exists in data but is not used for shortfall checks or “current net balance” per line.  
   - Need: current_net_balance (or equivalent) and shortfall logic for Scenario B.

5. **Requester sees raw lines**  
   - No aggregation by (item, location); no restriction to Status = Active (and Adjustment for net); voided/negative visible.  
   - Picking by GRN not restricted to Active and balance > 0.

6. **No mandatory reason codes**  
   - No TYP-01, PRC-02, LOC-03, DMG-04, DUP-05, MIS-06 or equivalent on corrections.

7. **No source_transaction_id**  
   - Adjustment entries do not exist; when we add them, each must link to the original entry (source_transaction_id).

8. **RBAC**  
   - Stores Officer should not have direct “edit quantity/price” on ledger; only “initiate correction”.  
   - Only Supervisor should execute adjustments and override shortfall alerts.

9. **PRC-02 (price) and LOC-03 (location)**  
   - No background job to sync Asset TCO when price is corrected.  
   - No forced Internal Transfer for location corrections.

---

## 3. Recommended Data Model (Ledger Approach)

Treat **one physical receipt or adjustment** as one row; never UPDATE quantity/price on that row.

### 3.1 Option A: Use `inventory_items` as Ledger (Preferred)

- **Add** to `inventory_items`:
  - `status` enum: `Active`, `Voided`, `Adjustment`.
  - `source_inventory_item_id` (nullable): for Adjustment rows, the id of the original line being voided/adjusted.
  - `current_net_balance` (nullable, or computed): original quantity minus SUM(sorder_parts.qty_supplied) for this id (denormalized for performance if needed).
- **Stop** updating `quantity` and `unit_cost_*` on existing rows for corrections.  
- **New entries only**:
  - Normal receive: status = `Active`.
  - Void: set original to `Voided`; insert Mirror (quantity = -Q, status = `Adjustment`, source_inventory_item_id = original id); insert New (quantity = correct Q, status = `Active`).
  - Partial reversal / negative adjustment: insert row with quantity = -N, status = `Adjustment`, source_inventory_item_id = original.

- **inventory_item_details**: Either repurpose as “history/audit” snapshot of each ledger row (append-only), or keep as duplicate of receipt for backward compatibility and add status/source there too; avoid double UPDATE of quantity/price.

### 3.2 New Tables for Workflow and Reason Codes

- **inventory_correction_requests**  
  - id, inventory_item_id (original line), requested_by (user_id), intended_quantity, intended_unit_cost (nullable), reason_code_id, status (pending / approved / rejected), approved_by, approved_at, notes, tenant_id, site_id, timestamps.

- **inventory_correction_reason_codes**  
  - id, code (TYP-01, PRC-02, …), type (Data Entry, Pricing, …), use_case description.

- **inventory_adjustments** (optional; if you want a single place for all “adjustment” rows)  
  - id, inventory_item_id (the new Adjustment row in inventory_items), correction_request_id, reason_code_id, adjustment_type (void_mirror, negative_adjustment, …), created_by, timestamps.  
  - Alternatively, keep this in application logic and only add source_inventory_item_id + reason on the ledger row or in a small “adjustment_metadata” table.

### 3.3 SorderPart and “Issued” Quantity

- Keep `sorder_parts.inventory_id` → `inventory_items.id`.  
- “Issued from this line” = COALESCE(SUM(sorder_parts.qty_supplied), 0) WHERE inventory_id = inventory_items.id.  
- Either store **current_net_balance** on `inventory_items` (updated when supplying or when applying adjustments) or compute on the fly. Stored is better for performance and for “GHOST STOCK” checks (correct amount - issued = shortfall).

---

## 4. Visibility and Queries (V4.0 Compliant)

### 4.1 Requester (Clean Search)

- **Availability by item/location**:  
  `SELECT item_id, location_id, site_id, SUM(quantity) AS available  
   FROM inventory_items  
   WHERE status IN ('Active', 'Adjustment') AND site_id = ? AND tenant_id = ?  
   GROUP BY item_id, location_id, site_id`  
  (and only show where available > 0).

- **Picking by GRN**:  
  Only show lines where `status = 'Active'` and `current_net_balance > 0` (or equivalent computed).

### 4.2 Stores Officer (Recent Entries / History)

- **Recent entries**: Same as today but include status; allow “Flag for Correction” only for their own entries (and only Active lines).
- **History**: Can show Active + Voided + Adjustment with clear labels; read-only for correction workflow.

### 4.3 Supervisor / Auditor

- **Full forensic view**:  
  `WHERE 1=1` (no status filter) so Status = ALL (Active, Voided, Adjustment).  
- **Correction queue**: List pending `inventory_correction_requests`; approve/reject with mandatory reason code.

---

## 5. Scenario Flows (Implementation)

### 5.1 Scenario A: Items NOT Yet Issued (Void & Re-issue)

1. **Initiation**: Stores Officer, on “Recent Entries”, clicks “Flag Error for Correction” on an **Active** line (inventory_item_id).  
2. **Request**: Form: intended_quantity (e.g. 2), optional intended_unit_cost; submit → creates `inventory_correction_requests` (status = pending).  
3. **Approval**: Supervisor approves (selects reason code TYP-01, DUP-05, etc.).  
4. **System action** (in one transaction):  
   - Set original row `status = 'Voided'`.  
   - Insert **Mirror**: same item/location/site/tenant, quantity = -original_quantity, status = `Adjustment`, source_inventory_item_id = original id.  
   - Insert **New**: quantity = intended_quantity, unit_cost = intended or original, status = `Active`, same GRN/inventory_id if desired (or new inventory header for traceability).  
   - Recompute `items.stock_quantity` from SUM(inventory_items.quantity) WHERE status IN ('Active','Adjustment').  
5. **Result**: Requester sees only the new 2 units; 22 and -22 are hidden from requester view, visible to Auditor.

### 5.2 Scenario B: Items HAVE Been Issued (Partial Reversal)

1. **Shortfall check**: When Stores Officer submits intended_quantity (e.g. 2), system computes:  
   - issued = SUM(sorder_parts.qty_supplied) WHERE inventory_id = original inventory_item_id.  
   - shortfall = intended_quantity - issued (e.g. 2 - 5 = -3).  
2. **Ghost stock alert**: If shortfall < 0, Supervisor sees: “Warning: Correcting to 2 units but 5 already issued. Shortfall -3.”  
3. **Adjustment**: Supervisor may still approve; system:  
   - Option A: Original line stays Active (parent of the 5 issued). Insert **negative adjustment** row (quantity = -20 or whatever brings net to desired state). Net balance = -3 → flags reconciliation for Planner.  
   - Option B: As per doc: “Supervisor executes a Negative Adjustment (-20)”; original remains Active; net balance becomes -3.  
4. **Reconciliation**: Planner (or future story) handles negative balance (return, write-off, or adjust demand).

### 5.3 Scenario C: GRN-Requester Filter (Clean Search)

- Implement aggregated availability query and “only Active + balance > 0” for GRN picking as in §4.1.  
- Ensure all requester-facing screens use these queries (no raw voided/negative lines).

---

## 6. Mandatory Reason Codes

- **Seed** table `inventory_correction_reason_codes`:  
  TYP-01 (Data Entry), PRC-02 (Pricing Error), LOC-03 (Site Mismatch), DMG-04 (Damaged/QA), DUP-05 (Duplicate Entry), MIS-06 (Mismatched GRN).  
- **Supervisor** must select one reason when approving a correction (dropdown on approve action).  
- **PRC-02**: On approve, trigger job to update Asset TCO for assets that consumed this item/GRN line (if such a concept exists in your schema).  
- **LOC-03**: Force creation of an Internal Transfer record instead of simply changing location (new table or reusing existing transfer logic if any).

---

## 7. RBAC and Permissions

- **Stores Officer**:  
  - Keep: add-grn, view-grn, received-history, supply-history.  
  - Add: `initiate-inventory-correction`.  
  - Remove or narrow: **edit-grn** so it does **not** allow direct update of quantity/unit_cost on ledger rows (only metadata like remarks, or remove edit-grn for line-level edits).  

- **Supervisor** (e.g. Super Authoriser or new “Inventory Supervisor” role):  
  - Add: `approve-inventory-correction`, `execute-inventory-adjustment`, `view-inventory-audit-log`.  
  - Only this role can approve correction requests and create Void/Mirror/New or negative adjustment rows.  

- **Requester**:  
  - No change to permissions; change **queries** so requester views use status filter and aggregation.

---

## 8. Implementation Phases (Suggested)

| Phase | Scope | Deliverables |
|-------|--------|--------------|
| **1. Schema & status** | Add status, source_inventory_item_id, optional current_net_balance; reason_codes and correction_requests tables; backfill status = 'Active' for existing rows. | Migrations; model changes; backfill. |
| **2. Requester visibility** | All requester search and picking use status IN ('Active','Adjustment') and aggregated view; GRN picking only Active and balance > 0. | StoreRequestController + any requester views. |
| **3. Stop direct edits** | Remove or gate direct UPDATE of quantity/price in InventoryController (update_inventory_item, inventory_history_action, update_inventory_history); allow only metadata edits or disable line-level edit for Stores Officer. | InventoryController; permission checks. |
| **4. Correction workflow** | “Flag for Correction” UI; correction request create; Supervisor queue and approve with reason code; Scenario A (void + mirror + new) execution. | New controller/actions; blades; routes. |
| **5. Scenario B & shortfall** | Compute issued per line; shortfall check; “Ghost stock” alert; negative adjustment creation; optional reconciliation placeholder. | Same controller + validation + alerts. |
| **6. PRC-02 / LOC-03** | On price correction approval, trigger TCO sync job; on LOC-03, create Internal Transfer. | Jobs; transfer service. |
| **7. Auditor view** | Full ledger view (all statuses) and correction history for Supervisor/Auditor. | New report or tab; queries. |

---

## 9. Files to Touch (Summary)

- **Migrations**: inventory_items (status, source_inventory_item_id, current_net_balance?), inventory_correction_reason_codes, inventory_correction_requests; optional inventory_adjustments or adjustment_metadata.
- **Models**: InventoryItem, new CorrectionRequest, ReasonCode; optionally Adjustment.
- **Controllers**: InventoryController (restrict direct quantity/price updates; add “flag” action); new InventoryCorrectionController (submit, list pending, approve, execute void+mirror+new and negative adjustment); StoreRequestController (requester queries as above).
- **Views**: Inventories index/edit (add “Flag for Correction”, hide direct qty/price edit for Store Officer); new Supervisor correction queue and approve form (with reason code); requester search results (aggregated if needed).
- **Policies/Permissions**: initiate-inventory-correction, approve-inventory-correction, execute-inventory-adjustment, view-inventory-audit-log; assign to Store Officer vs Supervisor.
- **Jobs**: Asset TCO sync when PRC-02 is approved (if applicable).
- **Internal Transfer**: LOC-03 flow (new or existing transfer module).

---

## 10. Audit Trail Preservation

- **Do not** DELETE or UPDATE quantity/price on existing ledger rows.  
- **Do** insert new rows only (Voided + Mirror + New, or Adjustment rows) with source_inventory_item_id and reason_code_id.  
- **Do** keep correction_requests with requested_by, approved_by, timestamps, and reason.  
- **Do** log in application logs (and optionally in an audit_events table) for each correction execution (who, when, which request, which inventory_item ids created/voided).  
- **Do** ensure items.stock_quantity is recomputed from SUM(quantity) WHERE status IN ('Active','Adjustment') so rollup stays correct.

This keeps a full forensic trail while meeting the “immutable ledger” and “additive inverse only” requirements of V4.0.
