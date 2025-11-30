# Resident Insertion Workflow with Auto-Assignment

## Overview
Implemented an automatic household head assignment system that triggers after resident insertion or deletion. The system uses AJAX to refresh data without full page reloads.

## Implementation Details

### 1. Auto-Assignment Logic (Household Model)

**New Method: `autoAssignHouseholdHead($household_id)`**

```php
Location: App/Model/Household.php
```

**Logic Rules:**
- **0 residents**: Set `household_head_id = NULL`
- **1 resident**: Auto-assign that resident as household head
- **Multiple residents**: 
  - If no head set: Assign first resident (earliest by `created_at`)
  - If current head deleted: Reassign to next resident (earliest by `created_at`)
  - If head still valid: Keep existing head

**Database Operations:**
```sql
-- When 0 residents
UPDATE households SET household_head_id = NULL WHERE household_id = ?

-- When 1 resident or reassignment needed
UPDATE households SET household_head_id = ? WHERE household_id = ?
```

### 2. API Endpoints (Controller)

**New Endpoints:**

#### `?action=addResident` (POST)
- Adds new resident to household
- Automatically calls `autoAssignHouseholdHead()`
- Returns: resident data + updated members list + household info + head assignment result

**Flow:**
```
1. Insert resident into database
2. Auto-assign household head based on count
3. Fetch updated members list
4. Fetch updated household info
5. Return complete dataset
```

#### `?action=deleteResident` (POST)
- Deletes resident from household
- Automatically reassigns head if deleted resident was head
- Returns: updated members list + household info + head assignment result

**Flow:**
```
1. Delete resident from database
2. Auto-reassign household head (if needed)
3. Fetch updated members list
4. Fetch updated household info
5. Return complete dataset
```

#### `?action=getMembersWithHead` (GET)
- Fetches members list + household info + current head ID
- Used for refreshing member display

### 3. Frontend Implementation

**Enhanced View Members Modal:**
- Added "Add New Member" collapsible form (Admin only)
- Shows household info: ID, Current Head, Total Members
- Delete buttons for each member (Admin only)
- Visual indicator (badge + highlight) for household head
- Auto-calculates age from birth date

**AJAX Workflow Functions:**

#### `refreshMembersList(householdId)`
Refreshes members display without page reload:
1. Fetches members + household data via AJAX
2. Updates household info display (head name, member count)
3. Rebuilds members table
4. Highlights current household head
5. All done without page reload

#### Add Resident Form Submission Handler
```javascript
1. Validate form inputs
2. POST to addResident endpoint
3. Receive: new resident + updated members + head assignment
4. Refresh members list via AJAX
5. Show success message with assignment info
6. Update main table household head cell
7. Collapse and reset form
```

#### Delete Resident Button Handler
```javascript
1. Confirm deletion
2. POST to deleteResident endpoint
3. Receive: updated members + new head assignment
4. Refresh members list via AJAX
5. Show success message with reassignment info
6. Update main table household head cell
```

### 4. Database Changes

**Modified Query in `getMembers()`:**
```sql
-- Added created_at to track insertion order
-- Changed ORDER BY from age DESC to created_at ASC
SELECT r.resident_id, r.first_name, r.middle_name, r.last_name, 
       r.birth_date, r.gender, r.age, r.contact_no, r.email, r.created_at
FROM residents r 
WHERE r.household_id = ? 
ORDER BY r.created_at ASC
```

This ensures first-inserted resident becomes head when multiple residents exist.

## Usage Examples

### Example 1: Adding First Resident
```
Before: household_head_id = NULL, member_count = 0
Action: Add resident "Juan Dela Cruz"
After:  household_head_id = R001 (Juan), member_count = 1
Result: "Single resident auto-assigned as household head"
```

### Example 2: Adding Second Resident
```
Before: household_head_id = R001 (Juan), member_count = 1
Action: Add resident "Maria Dela Cruz"
After:  household_head_id = R001 (Juan), member_count = 2
Result: "Household head is valid" (Juan remains head)
```

### Example 3: Deleting Current Head
```
Before: household_head_id = R001 (Juan), members = [R001, R002, R003]
Action: Delete R001 (Juan)
After:  household_head_id = R002 (next oldest), members = [R002, R003]
Result: "Household head reassigned to first resident"
```

### Example 4: Deleting Last Resident
```
Before: household_head_id = R001, member_count = 1
Action: Delete R001
After:  household_head_id = NULL, member_count = 0
Result: "No residents, head set to NULL"
```

## Key Features

### ✅ No Page Reloads
- All operations use AJAX/fetch
- Only affected sections refresh
- Smooth user experience

### ✅ Automatic Head Assignment
- Triggered after every insert/delete
- Follows clear business rules
- Ensures data consistency

### ✅ Real-time Updates
- Members list refreshes immediately
- Household info updates instantly
- Main table head column updates

### ✅ Visual Feedback
- Loading spinners during operations
- Success/error messages with SweetAlert2
- Household head highlighted with badge
- Current head row highlighted in blue

### ✅ Data Integrity
- No orphaned household_head_id references
- Automatic reassignment prevents invalid states
- Transaction-safe operations

## Technical Flow Diagram

```
ADD RESIDENT WORKFLOW:
┌─────────────────────────────────────┐
│ User clicks "Add New Member"        │
│ Fills form, clicks Add              │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ POST /HouseholdController.php       │
│ ?action=addResident                 │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Resident->create($data)             │
│ INSERT INTO residents               │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Household->autoAssignHouseholdHead()│
│ - Count members                     │
│ - Apply assignment logic            │
│ - UPDATE households SET head        │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Fetch updated data:                 │
│ - getMembers()                      │
│ - getById()                         │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ Return JSON response:               │
│ {                                   │
│   success: true,                    │
│   members: [...],                   │
│   household: {...},                 │
│   head_assignment: {...}            │
│ }                                   │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ JavaScript receives response        │
│ - refreshMembersList() [AJAX]       │
│ - Update UI (no page reload)        │
│ - Show success message              │
└─────────────────────────────────────┘
```

## Files Modified

### 1. `App/Model/Household.php`
- Modified `getMembers()` to include `created_at` and order by insertion
- Added `autoAssignHouseholdHead()` method

### 2. `App/Controller/HouseholdController.php`
- Added `handleAddResident()` endpoint
- Added `handleDeleteResident()` endpoint
- Added `handleGetMembersWithHead()` endpoint
- Updated switch statement to route new actions

### 3. `App/View/household.php`
- Enhanced View Members Modal with:
  - Household info display
  - Add resident form (collapsible)
  - Delete buttons per member
  - Household head visual indicators
- Added JavaScript functions:
  - `refreshMembersList()` - AJAX refresh
  - Add resident form handler
  - Delete resident button handler
  - Auto-calculate age

## Testing Checklist

- [x] Add first resident → auto-assigned as head
- [x] Add second resident → first remains head
- [x] Delete current head → next resident becomes head
- [x] Delete last resident → head set to NULL
- [x] Members list refreshes without page reload
- [x] Household head displayed correctly in modal
- [x] Main table head column updates after operations
- [x] Age auto-calculation works
- [x] Admin-only forms and buttons respect RBAC
- [x] Error handling for failed operations

## Security Notes

- Only Admin (role_id = 1) can add/delete residents
- RBAC checks in controller before operations
- Authentication required for all endpoints
- Input validation on both frontend and backend
- SQL injection prevention via prepared statements

## Performance Considerations

- AJAX operations reduce server load (partial updates only)
- Indexed queries on household_id for fast member lookups
- Single transaction for insert + auto-assign
- Minimal data transfer (only changed data)

## Future Enhancements

Potential improvements:
- Edit resident inline
- Bulk resident import
- Manual head selection override
- Audit log for head changes
- Notification when head is auto-reassigned
- Support for household head history
