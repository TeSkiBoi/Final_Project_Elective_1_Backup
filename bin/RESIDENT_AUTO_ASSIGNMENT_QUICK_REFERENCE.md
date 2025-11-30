# Quick Reference: Resident Auto-Assignment System

## API Endpoints

### Add Resident with Auto-Assignment
```javascript
POST /App/Controller/HouseholdController.php?action=addResident

Request Body:
{
  "household_id": "HH001",
  "first_name": "Juan",
  "middle_name": "Santos",
  "last_name": "Dela Cruz",
  "birth_date": "1990-05-15",
  "age": 33,
  "gender": "Male",
  "contact_no": "09171234567",
  "email": "juan@example.com"
}

Response:
{
  "success": true,
  "message": "Resident created successfully!",
  "resident_id": "R001",
  "members": [ /* array of all members */ ],
  "household": { /* household details */ },
  "head_assignment": {
    "success": true,
    "message": "Single resident auto-assigned as household head",
    "household_head_id": "R001",
    "member_count": 1
  }
}
```

### Delete Resident with Auto-Reassignment
```javascript
POST /App/Controller/HouseholdController.php?action=deleteResident

Request Body:
{
  "resident_id": "R001",
  "household_id": "HH001"
}

Response:
{
  "success": true,
  "message": "Resident deleted successfully",
  "members": [ /* remaining members */ ],
  "household": { /* updated household */ },
  "head_assignment": {
    "success": true,
    "message": "Household head reassigned to first resident",
    "household_head_id": "R002",
    "member_count": 2
  }
}
```

### Get Members with Head Info
```javascript
GET /App/Controller/HouseholdController.php?action=getMembersWithHead&household_id=HH001

Response:
{
  "success": true,
  "members": [ /* array of members */ ],
  "household": { /* household details */ },
  "household_head_id": "R001"
}
```

## Auto-Assignment Rules

| Member Count | Current Head | Action Taken |
|-------------|--------------|--------------|
| 0           | Any          | Set to NULL  |
| 1           | Any          | Auto-assign single resident as head |
| 2+          | NULL         | Assign first resident (by created_at) |
| 2+          | Valid        | Keep current head |
| 2+          | Deleted      | Reassign to first remaining resident |

## JavaScript Usage

### Refresh Members List (AJAX)
```javascript
// Call this after any add/delete operation
await refreshMembersList(householdId);
```

### Add Resident Programmatically
```javascript
const response = await fetch(API_URL + '?action=addResident', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        household_id: 'HH001',
        first_name: 'Juan',
        last_name: 'Dela Cruz',
        birth_date: '1990-05-15',
        age: 33,
        gender: 'Male',
        contact_no: '09171234567',
        email: 'juan@example.com'
    })
});

const result = await response.json();
if (result.success) {
    // Member added and head auto-assigned
    console.log('New head:', result.head_assignment.household_head_id);
    await refreshMembersList(householdId); // Refresh display
}
```

### Delete Resident Programmatically
```javascript
const response = await fetch(API_URL + '?action=deleteResident', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        resident_id: 'R001',
        household_id: 'HH001'
    })
});

const result = await response.json();
if (result.success) {
    // Member deleted and head reassigned if needed
    console.log('New head:', result.head_assignment.household_head_id);
    await refreshMembersList(householdId); // Refresh display
}
```

## PHP Usage

### Manual Head Assignment
```php
require_once 'App/Model/Household.php';
$householdModel = new Household();

$result = $householdModel->autoAssignHouseholdHead('HH001');

if ($result['success']) {
    echo "Head assigned: " . $result['household_head_id'];
    echo "Member count: " . $result['member_count'];
}
```

### Get Members with Created Date
```php
$members = $householdModel->getMembers('HH001');
// Returns array ordered by created_at ASC (earliest first)
// First member in array is the one assigned as head if no head exists
```

## UI Elements

### View Members Modal Structure
```
┌──────────────────────────────────────────────────┐
│ Household Members - [Name]                    [X]│
├──────────────────────────────────────────────────┤
│ [Household Info Display]                         │
│ ID: HH001 | Head: Juan Dela Cruz | Members: 3    │
├──────────────────────────────────────────────────┤
│ [+ Add New Member] (Collapsible Form)            │
├──────────────────────────────────────────────────┤
│ [Members Table]                                  │
│ ID    | Name          | Age | Gender | [Delete] │
│ R001  | Juan D.C. 🏅  | 33  | Male   | [🗑]     │
│ R002  | Maria D.C.    | 30  | Female | [🗑]     │
│ R003  | Pedro D.C.    | 10  | Male   | [🗑]     │
└──────────────────────────────────────────────────┘

Legend:
🏅 = Household Head Badge
🗑 = Delete Button (Admin only)
```

### Add Resident Form Fields
- First Name* (required)
- Middle Name (optional)
- Last Name* (required)
- Birth Date* (required)
- Age (auto-calculated, readonly)
- Gender* (Male/Female, required)
- Contact No (optional)
- Email (optional)

## Common Scenarios

### Scenario 1: New Household Setup
```
1. Create household → head = NULL
2. Add first resident → head = R001 (auto)
3. Members list refreshes → shows R001 with 🏅 badge
```

### Scenario 2: Adding Family Members
```
1. Household has head = R001 (Juan)
2. Add wife R002 (Maria) → head stays R001
3. Add child R003 (Pedro) → head stays R001
4. Members list refreshes → Juan still has 🏅 badge
```

### Scenario 3: Head Leaves Household
```
1. Household: R001 (head), R002, R003
2. Delete R001 → head auto-reassigns to R002
3. Members list refreshes → R002 now has 🏅 badge
4. Success message: "Household head has been reassigned"
```

### Scenario 4: Last Member Leaves
```
1. Household: R001 (head, only member)
2. Delete R001 → head = NULL
3. Members list shows: "No members found"
4. Success message: "No members remain in household"
```

## Troubleshooting

### Issue: Head not auto-assigning
**Check:**
- Verify `created_at` column exists in `residents` table
- Check error logs for `autoAssignHouseholdHead()` errors
- Ensure foreign key constraints allow updates

### Issue: Members list not refreshing
**Check:**
- Browser console for JavaScript errors
- Network tab for failed AJAX requests
- Verify `refreshMembersList()` is being called
- Check API endpoint returns proper JSON

### Issue: Delete fails
**Check:**
- Foreign key constraints on `household_head_id`
- Cascade delete rules on `residents` table
- User has Admin role (role_id = 1)

## Debug Mode

Enable logging to track auto-assignment:
```php
// Already implemented in Household.php
error_log("Auto-assign head for household {$household_id}: {$memberCount} members found");
error_log("Single member - auto-assigned {$residentId} as head");
error_log("Multiple members, head reassigned to {$newHeadId}");
```

Check PHP error logs:
```bash
# Windows XAMPP
tail -f C:\xampp\php\logs\php_error_log

# Linux
tail -f /var/log/apache2/error.log
```

## Performance Tips

1. **Batch Operations**: If adding multiple residents, consider batch insert then single auto-assign call
2. **Caching**: Consider caching household head in session for repeated lookups
3. **Indexing**: Ensure `household_id` and `created_at` are indexed
4. **Pagination**: For households with many members, implement pagination

## Security Checklist

- ✅ RBAC enforced (Admin only for add/delete)
- ✅ Authentication required for all endpoints
- ✅ SQL injection prevention (prepared statements)
- ✅ Input validation on both frontend and backend
- ✅ XSS prevention (htmlspecialchars on output)
- ✅ CSRF protection (consider adding CSRF tokens)
