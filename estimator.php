<?php
/**
 * Standalone Trip Budget Estimator Tool
 * 
 * Allows users to calculate realistic travel budgets in INR based on destination,
 * travel duration, number of persons, and travel style with immediate breakdown.
 * Links directly to add_trip.php with pre-filled parameters.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Trip Budget Estimator";
require_once 'header.php';
?>

<div style="max-width: 860px; margin: 0 auto;">
    <div class="card">
        <div class="card-header text-center">
            <h2>🧮 Trip Budget Estimator</h2>
            <p>Calculate realistic travel expenses in Indian Rupees (₹ INR) for your upcoming holiday before you book.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 2rem; align-items: start;">
            <!-- Calculator Inputs -->
            <div>
                <h3 style="font-size: 1.15rem; color: var(--dark); margin-bottom: 1.25rem;">1. Trip Details</h3>

                <div class="form-group">
                    <label for="est_dest">Target Destination</label>
                    <input type="text" id="est_dest" class="input-control" placeholder="e.g. Manali, Goa, Kerala, or Dubai" value="Goa Beaches, India" oninput="runEstimator()">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="est_duration">Trip Days</label>
                        <input type="number" id="est_duration" class="input-control" value="5" min="1" max="60" oninput="runEstimator()">
                    </div>

                    <div class="form-group">
                        <label for="est_people">Number of Persons</label>
                        <input type="number" id="est_people" class="input-control" value="2" min="1" max="50" oninput="runEstimator()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="est_region">Destination Scope</label>
                    <select id="est_region" class="input-control" onchange="runEstimator()">
                        <option value="domestic" selected>🇮🇳 Domestic (India)</option>
                        <option value="international">✈️ International (Overseas)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Travel Comfort Level</label>
                    <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                        <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; background: #ffffff;">
                            <input type="radio" name="travel_style" value="budget" onchange="runEstimator()">
                            <div>
                                <strong style="color: var(--dark);">🎒 Budget / Backpacker</strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Hostels, street eats, local buses &amp; trains (~₹2,500/day/person)</div>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; border: 1px solid var(--primary); border-radius: var(--radius-sm); cursor: pointer; background: var(--primary-light);">
                            <input type="radio" name="travel_style" value="standard" checked onchange="runEstimator()">
                            <div>
                                <strong style="color: var(--primary-dark);">🏨 Comfort / Standard (Recommended)</strong>
                                <div style="font-size: 0.8rem; color: var(--dark-muted);">3-star hotels, nice restaurants, taxis &amp; activities (~₹5,500/day/person)</div>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer; background: #ffffff;">
                            <input type="radio" name="travel_style" value="luxury" onchange="runEstimator()">
                            <div>
                                <strong style="color: var(--dark);">👑 Luxury / Premium</strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">5-star resorts, private chauffeurs, fine dining (~₹14,000/day/person)</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Calculation Output & Breakdown -->
            <div>
                <h3 style="font-size: 1.15rem; color: var(--dark); margin-bottom: 1.25rem;">2. Estimated Expense Breakdown</h3>

                <div style="background: #ffffff; border: 2px solid var(--primary-light); border-radius: var(--radius-md); padding: 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <div style="text-align: center; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--border-color);">
                        <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">
                            Total Estimated Budget
                        </div>
                        <div id="display_total" style="font-size: 2.25rem; font-weight: 800; color: var(--primary); margin: 0.25rem 0;">
                            ₹55,000.00
                        </div>
                        <div id="display_per_person" style="font-size: 0.95rem; color: var(--dark-muted); font-weight: 500;">
                            ₹27,500.00 per person
                        </div>
                    </div>

                    <!-- Category Items -->
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        <div class="d-flex justify-between align-center" style="font-size: 0.9rem;">
                            <span style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                                <span>🏨</span> Accommodation &amp; Hotel (40%)
                            </span>
                            <strong id="item_stay" style="color: var(--dark);">₹22,000</strong>
                        </div>
                        <div class="d-flex justify-between align-center" style="font-size: 0.9rem;">
                            <span style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                                <span>🍽️</span> Meals &amp; Dining (25%)
                            </span>
                            <strong id="item_food" style="color: var(--dark);">₹13,750</strong>
                        </div>
                        <div class="d-flex justify-between align-center" style="font-size: 0.9rem;">
                            <span style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                                <span>🚕</span> Local Transport &amp; Transit (20%)
                            </span>
                            <strong id="item_transport" style="color: var(--dark);">₹11,000</strong>
                        </div>
                        <div class="d-flex justify-between align-center" style="font-size: 0.9rem;">
                            <span style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                                <span>🎟️</span> Sightseeing, Guides &amp; Buffer (15%)
                            </span>
                            <strong id="item_activities" style="color: var(--dark);">₹8,250</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div style="text-align: center;">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button type="button" class="btn btn-primary btn-block" onclick="proceedToPlan()">
                            ➕ Plan Trip With This Estimated Budget &rarr;
                        </button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-block">
                            🔑 Log In to Save Trip Plan &rarr;
                        </a>
                        <div style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.5rem;">
                            Don't have an account? <a href="register.php" style="color: var(--primary);">Register free</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentCalculatedTotal = 55000;

function runEstimator() {
    const dest = document.getElementById('est_dest').value;
    const days = parseInt(document.getElementById('est_duration').value) || 1;
    const persons = parseInt(document.getElementById('est_people').value) || 1;
    const scope = document.getElementById('est_region').value;
    
    const radios = document.getElementsByName('travel_style');
    let tier = 'standard';
    for (let r of radios) {
        if (r.checked) {
            tier = r.value;
            break;
        }
    }

    const rates = {
        domestic: {
            budget: 2500,
            standard: 5500,
            luxury: 14000
        },
        international: {
            budget: 6500,
            standard: 13500,
            luxury: 32000
        }
    };

    const rate = rates[scope][tier];
    const total = days * persons * rate;
    const perPerson = total / persons;

    currentCalculatedTotal = total;

    const stay = Math.round(total * 0.40);
    const food = Math.round(total * 0.25);
    const transport = Math.round(total * 0.20);
    const activities = Math.round(total * 0.15);

    document.getElementById('display_total').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('display_per_person').textContent = '₹' + Math.round(perPerson).toLocaleString('en-IN') + ' per person (' + persons + ' traveller' + (persons > 1 ? 's' : '') + ')';

    document.getElementById('item_stay').textContent = '₹' + stay.toLocaleString('en-IN');
    document.getElementById('item_food').textContent = '₹' + food.toLocaleString('en-IN');
    document.getElementById('item_transport').textContent = '₹' + transport.toLocaleString('en-IN');
    document.getElementById('item_activities').textContent = '₹' + activities.toLocaleString('en-IN');
}

function proceedToPlan() {
    const dest = encodeURIComponent(document.getElementById('est_dest').value);
    const persons = encodeURIComponent(document.getElementById('est_people').value);
    const budget = encodeURIComponent(currentCalculatedTotal.toFixed(2));
    window.location.href = `add_trip.php?destination=${dest}&persons=${persons}&budget=${budget}`;
}

window.addEventListener('DOMContentLoaded', runEstimator);
</script>

<?php require_once 'footer.php'; ?>
