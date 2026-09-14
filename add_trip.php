<?php
/**
 * Add New Trip Plan (Protected Page)
 * 
 * Stores visitor trip plans with destination, travel date, number of persons,
 * budget, visitor email, and itinerary notes. Includes an interactive
 * Trip Budget Estimator to calculate expenses in real-time.
 */
require_once 'db.php';

// Safe session startup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect unauthenticated users directly to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = '';
// Pre-fill from POST or GET (e.g. redirected from budget estimator)
$destination   = trim($_POST['destination'] ?? $_GET['destination'] ?? '');
$travel_date   = trim($_POST['travel_date'] ?? $_GET['travel_date'] ?? '');
$persons_input = trim($_POST['persons'] ?? $_GET['persons'] ?? '1');
$budget_input  = trim($_POST['budget'] ?? $_GET['budget'] ?? '');
$visitor_email = trim($_POST['visitor_email'] ?? $_SESSION['email'] ?? '');
$notes         = trim($_POST['notes'] ?? $_GET['notes'] ?? '');

$persons = (int)$persons_input;
if ($persons < 1) {
    $persons = 1;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination   = trim($_POST['destination'] ?? '');
    $travel_date   = trim($_POST['travel_date'] ?? '');
    $persons_input = trim($_POST['persons'] ?? '1');
    $budget_input  = trim($_POST['budget'] ?? '');
    $visitor_email = trim($_POST['visitor_email'] ?? '');
    $notes         = trim($_POST['notes'] ?? '');

    $persons = (int)$persons_input;

    // Server-side validation
    if (empty($destination) || empty($travel_date) || empty($budget_input) || empty($visitor_email)) {
        $error_message = "Please complete all mandatory fields marked with an asterisk (*).";
    } elseif ($persons < 1 || $persons > 100) {
        $error_message = "Number of persons must be between 1 and 100.";
    } elseif (!filter_var($visitor_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid visitor contact email address.";
    } elseif (!is_numeric($budget_input) || floatval($budget_input) < 0) {
        $error_message = "Budget must be a positive numeric value in Rupees (e.g. 25000.00).";
    } else {
        $user_id = (int)$_SESSION['user_id'];
        $budget  = floatval($budget_input);

        // SQL Prepared Statement for secure trip insertion with persons
        $sql = "INSERT INTO trips (user_id, destination, travel_date, persons, budget, visitor_email, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Types: i = integer, s = string, d = double -> "issidss"
            mysqli_stmt_bind_param($stmt, "issidss", $user_id, $destination, $travel_date, $persons, $budget, $visitor_email, $notes);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                // Redirect to trips listing with success notification
                header("Location: view_trips.php?msg=added");
                exit();
            } else {
                $error_message = "Failed to save trip to database. Please check your data and try again.";
                mysqli_stmt_close($stmt);
            }
        } else {
            $error_message = "Database query preparation failed.";
        }
    }
}

$page_title = "Plan New Trip";
require_once 'header.php';
?>

<div style="max-width: 740px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-between align-center" style="flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h2>Plan a New Tourist Visit</h2>
                    <p>Enter destination, date, number of travellers, and your estimated trip budget.</p>
                </div>
                <button type="button" id="toggle-estimator-btn" class="btn btn-outline btn-sm" onclick="toggleEstimator()">
                    🧮 Open Budget Estimator
                </button>
            </div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <div><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endif; ?>

        <!-- ================================================================
             Interactive Trip Budget Estimator Assistant (Collapsible / Real-time)
             ================================================================ -->
        <div id="budget-estimator-panel" style="display: none; background: #f0fdfa; border: 1px solid #99f6e4; border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
            <div class="d-flex justify-between align-center" style="margin-bottom: 0.75rem;">
                <h3 style="font-size: 1.05rem; color: #0f766e; display: flex; align-items: center; gap: 0.4rem;">
                    <span>🧮</span> Real-Time Trip Budget Estimator
                </h3>
                <span style="font-size: 0.8rem; color: #0d9488; font-weight: 600;">Auto-Calculation</span>
            </div>
            <p style="font-size: 0.85rem; color: #134e4a; margin-bottom: 1rem;">
                Calculate an accurate estimated budget based on your trip length, group size, and travel style.
            </p>

            <div class="form-grid-2" style="margin-bottom: 1rem;">
                <div>
                    <label for="est_days" style="font-size: 0.825rem; color: #0f766e;">Trip Duration (Days)</label>
                    <input type="number" id="est_days" class="input-control" value="5" min="1" max="60" oninput="calculateEstimate()">
                </div>
                <div>
                    <label for="est_scope" style="font-size: 0.825rem; color: #0f766e;">Trip Destination Scope</label>
                    <select id="est_scope" class="input-control" onchange="calculateEstimate()">
                        <option value="domestic" selected>Domestic (India)</option>
                        <option value="international">International</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-size: 0.825rem; color: #0f766e;">Travel Style / Comfort Level</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.4rem; background: #ffffff; padding: 0.5rem 0.75rem; border: 1px solid #ccfbf1; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                        <input type="radio" name="est_tier" value="budget" onchange="calculateEstimate()">
                        🎒 Budget (~₹2.5k/d)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.4rem; background: #ffffff; padding: 0.5rem 0.75rem; border: 1px solid #ccfbf1; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                        <input type="radio" name="est_tier" value="standard" checked onchange="calculateEstimate()">
                        🏨 Comfort (~₹5.5k/d)
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.4rem; background: #ffffff; padding: 0.5rem 0.75rem; border: 1px solid #ccfbf1; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                        <input type="radio" name="est_tier" value="luxury" onchange="calculateEstimate()">
                        👑 Luxury (~₹14k/d)
                    </label>
                </div>
            </div>

            <!-- Calculated Breakdown Display -->
            <div style="background: #ffffff; border: 1px solid #ccfbf1; border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.5rem; text-align: center; font-size: 0.8rem; color: #475569;">
                    <div style="padding: 0.4rem; background: #f8fafc; border-radius: 4px;">
                        <div style="color: #64748b;">🏨 Stay</div>
                        <strong id="breakdown_stay" style="color: #0f172a; font-size: 0.95rem;">₹0</strong>
                    </div>
                    <div style="padding: 0.4rem; background: #f8fafc; border-radius: 4px;">
                        <div style="color: #64748b;">🍽️ Food</div>
                        <strong id="breakdown_food" style="color: #0f172a; font-size: 0.95rem;">₹0</strong>
                    </div>
                    <div style="padding: 0.4rem; background: #f8fafc; border-radius: 4px;">
                        <div style="color: #64748b;">🚕 Transport</div>
                        <strong id="breakdown_transport" style="color: #0f172a; font-size: 0.95rem;">₹0</strong>
                    </div>
                    <div style="padding: 0.4rem; background: #f8fafc; border-radius: 4px;">
                        <div style="color: #64748b;">🎟️ Activities</div>
                        <strong id="breakdown_activities" style="color: #0f172a; font-size: 0.95rem;">₹0</strong>
                    </div>
                </div>

                <div class="d-flex justify-between align-center" style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed #e2e8f0; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <span style="font-size: 0.85rem; color: #64748b;">Estimated Total: </span>
                        <strong id="est_total_display" style="font-size: 1.25rem; color: #0f766e;">₹0.00</strong>
                        <span id="est_per_person_display" style="font-size: 0.8rem; color: #0d9488; margin-left: 0.35rem;">(₹0 / person)</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="applyEstimatedBudget()">
                        ⚡ Apply to Budget Field
                    </button>
                </div>
            </div>
        </div>

        <form action="add_trip.php" method="POST" novalidate>
            <!-- Destination Input -->
            <div class="form-group">
                <label for="destination">Tourist Destination <span style="color: var(--danger);">*</span></label>
                <input 
                    type="text" 
                    id="destination" 
                    name="destination" 
                    class="input-control" 
                    placeholder="e.g. Goa Beaches, Manali, Jaipur, or Paris" 
                    value="<?php echo htmlspecialchars($destination, ENT_QUOTES, 'UTF-8'); ?>" 
                    required 
                    maxlength="100"
                    autofocus
                >
                <div class="input-hint">City, tourist region, or famous landmark.</div>
            </div>

            <!-- Travel Date & Persons Grid -->
            <div class="form-grid-2">
                <!-- Travel Date Input -->
                <div class="form-group">
                    <label for="travel_date">Travel Date <span style="color: var(--danger);">*</span></label>
                    <input 
                        type="date" 
                        id="travel_date" 
                        name="travel_date" 
                        class="input-control" 
                        value="<?php echo htmlspecialchars($travel_date, ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                    >
                </div>

                <!-- Number of Persons Input -->
                <div class="form-group">
                    <label for="persons">Number of Persons <span style="color: var(--danger);">*</span></label>
                    <input 
                        type="number" 
                        id="persons" 
                        name="persons" 
                        class="input-control" 
                        placeholder="e.g. 2" 
                        min="1" 
                        max="100" 
                        value="<?php echo htmlspecialchars((string)$persons, ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                        oninput="syncPersonsToEstimator()"
                    >
                    <div class="input-hint">Solo traveller, couple (2), or group size.</div>
                </div>
            </div>

            <!-- Estimated Budget (₹ INR) Input -->
            <div class="form-group">
                <div class="d-flex justify-between align-center" style="margin-bottom: 0.45rem;">
                    <label for="budget" style="margin-bottom: 0;">Estimated Budget (₹ INR) <span style="color: var(--danger);">*</span></label>
                    <a href="javascript:void(0)" onclick="toggleEstimator(true)" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; text-decoration: none;">
                        💡 Need help estimating? Use Calculator
                    </a>
                </div>
                <input 
                    type="number" 
                    id="budget" 
                    name="budget" 
                    class="input-control" 
                    placeholder="e.g. 45000.00" 
                    step="0.01" 
                    min="0" 
                    value="<?php echo htmlspecialchars($budget_input, ENT_QUOTES, 'UTF-8'); ?>" 
                    required
                >
                <div class="input-hint">Total estimated trip budget for all persons in Indian Rupees.</div>
            </div>

            <!-- Visitor Contact Email -->
            <div class="form-group">
                <label for="visitor_email">Visitor Contact Email <span style="color: var(--danger);">*</span></label>
                <input 
                    type="email" 
                    id="visitor_email" 
                    name="visitor_email" 
                    class="input-control" 
                    placeholder="e.g. yourname@example.com" 
                    value="<?php echo htmlspecialchars($visitor_email, ENT_QUOTES, 'UTF-8'); ?>" 
                    required 
                    maxlength="100"
                >
                <div class="input-hint">Email address for trip booking details, reminders, and confirmations.</div>
            </div>

            <!-- Travel Notes & Itinerary Highlights -->
            <div class="form-group">
                <label for="notes">Itinerary Notes &amp; Highlights</label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    class="input-control" 
                    placeholder="List must-see tourist spots, hotel bookings, flight/train details, or packing checklists..."
                    rows="4"
                ><?php echo htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="d-flex justify-between align-center" style="margin-top: 2rem; flex-wrap: wrap; gap: 1rem;">
                <a href="view_trips.php" class="btn btn-outline">
                    &larr; Back to My Trips
                </a>
                <button type="submit" class="btn btn-primary">
                    💾 Save Trip Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Interactive Client-Side Trip Budget Calculation Engine
function toggleEstimator(forceOpen = false) {
    const panel = document.getElementById('budget-estimator-panel');
    const btn = document.getElementById('toggle-estimator-btn');
    if (forceOpen || panel.style.display === 'none') {
        panel.style.display = 'block';
        btn.textContent = '✕ Close Estimator';
        calculateEstimate();
    } else {
        panel.style.display = 'none';
        btn.textContent = '🧮 Open Budget Estimator';
    }
}

function syncPersonsToEstimator() {
    calculateEstimate();
}

function calculateEstimate() {
    const days = parseInt(document.getElementById('est_days').value) || 1;
    const persons = parseInt(document.getElementById('persons').value) || 1;
    const scope = document.getElementById('est_scope').value;
    const tierRadios = document.getElementsByName('est_tier');
    
    let tier = 'standard';
    for (let r of tierRadios) {
        if (r.checked) {
            tier = r.value;
            break;
        }
    }

    // Daily expense rates per person in INR
    const rates = {
        domestic: {
            budget: 2500,    // Guesthouse/hostel, street food, public transit
            standard: 5500,  // 3-star hotel, cabs, quality restaurant dining
            luxury: 14000    // 5-star resorts, private vehicles, gourmet dining
        },
        international: {
            budget: 6500,
            standard: 13500,
            luxury: 32000
        }
    };

    const dailyRatePerPerson = rates[scope][tier];
    const totalEstimate = days * persons * dailyRatePerPerson;
    const perPerson = totalEstimate / persons;

    // Component Breakdowns
    const stayCost = Math.round(totalEstimate * 0.40);
    const foodCost = Math.round(totalEstimate * 0.25);
    const transportCost = Math.round(totalEstimate * 0.20);
    const activitiesCost = Math.round(totalEstimate * 0.15);

    document.getElementById('breakdown_stay').textContent = '₹' + stayCost.toLocaleString('en-IN');
    document.getElementById('breakdown_food').textContent = '₹' + foodCost.toLocaleString('en-IN');
    document.getElementById('breakdown_transport').textContent = '₹' + transportCost.toLocaleString('en-IN');
    document.getElementById('breakdown_activities').textContent = '₹' + activitiesCost.toLocaleString('en-IN');

    document.getElementById('est_total_display').textContent = '₹' + totalEstimate.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('est_per_person_display').textContent = '(₹' + Math.round(perPerson).toLocaleString('en-IN') + ' / person)';

    window._calculatedBudget = totalEstimate;
}

function applyEstimatedBudget() {
    if (window._calculatedBudget) {
        document.getElementById('budget').value = window._calculatedBudget.toFixed(2);
        // Highlight budget input temporarily
        const budgetInput = document.getElementById('budget');
        budgetInput.style.borderColor = '#10b981';
        budgetInput.style.backgroundColor = '#ecfdf5';
        setTimeout(() => {
            budgetInput.style.borderColor = '';
            budgetInput.style.backgroundColor = '';
        }, 1200);
    }
}

// Pre-calculate on load
window.addEventListener('DOMContentLoaded', () => {
    // If user arrived with GET params, calculate
    calculateEstimate();
});
</script>

<?php require_once 'footer.php'; ?>
