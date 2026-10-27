<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Membership Plan</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 25px 15px;
            background: #071426;
            color: #eaf1fb;
            font-family: Arial, sans-serif;
        }

        .panel {
            max-width: 700px;
            margin: 20px auto;
            padding: 25px;
            background: #10233c;
            border: 1px solid #203754;
            border-radius: 12px;
        }

        h1 { margin-top: 0; }
        .muted { color: #9fb1c9; }

        label {
            display: block;
            margin: 17px 0 7px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            background: #071426;
            color: #eaf1fb;
            border: 1px solid #38516e;
            border-radius: 7px;
            font: inherit;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .duration-options {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 10px 0 16px;
        }

        .duration-option {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 12px 16px;
            border: 1px solid #38516e;
            border-radius: 8px;
            background: #071426;
            cursor: pointer;
        }

        .duration-option input {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: #3b82f6;
            cursor: pointer;
        }

        .duration-option span {
            font-weight: bold;
        }

        .button {
            display: inline-block;
            padding: 12px 16px;
            background: #2563eb;
            color: white;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        .secondary { background: #263b56; }

        .error {
            color: #fecaca;
            font-size: 13px;
            margin-top: 5px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
<div class="panel">
    <h1>Add Membership Plan</h1>
    <p class="muted">Create a package for your gym members.</p>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('membership-plans.store') }}">
        @csrf

        <label for="name">Plan Name *</label>
        <input
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="e.g. Monthly Membership"
            required
            maxlength="100"
        >
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            maxlength="2000"
            placeholder="Describe what's included in this plan"
        >{{ old('description') }}</textarea>
        @error('description') <div class="error">{{ $message }}</div> @enderror

        <label for="price">Price (PHP) *</label>
        <input
            id="price"
            type="number"
            name="price"
            value="{{ old('price', '0.00') }}"
            min="0"
            max="99999999.99"
            step="0.01"
            required
        >
        @error('price') <div class="error">{{ $message }}</div> @enderror

        <label>Duration Unit *</label>

        <div class="duration-options">
            <label class="duration-option" for="unit_days">
                <input
                    id="unit_days"
                    type="checkbox"
                    name="duration_unit"
                    value="days"
                    @checked(old('duration_unit', 'days') === 'days')
                >
                <span>Days</span>
            </label>

            <label class="duration-option" for="unit_hours">
                <input
                    id="unit_hours"
                    type="checkbox"
                    name="duration_unit"
                    value="hours"
                    @checked(old('duration_unit') === 'hours')
                >
                <span>Hours</span>
            </label>
        </div>

        <div id="duration-error" class="error" style="display:none;">
            Please select Days or Hours.
        </div>

        <label id="duration-label" for="duration_days">
            Duration (days) *
        </label>

        <input
            id="duration_days"
            type="number"
            name="duration_days"
            value="{{ old('duration_days', 30) }}"
            min="1"
            max="3650"
            step="1"
            required
        >

        @error('duration_days')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="is_active">Status *</label>
        <select id="is_active" name="is_active" required>
            <option value="1" @selected(old('is_active', '1') == '1')>
                Active
            </option>
            <option value="0" @selected(old('is_active') === '0')>
                Inactive
            </option>
        </select>

        <div class="actions">
            <button class="button" type="submit">Save Plan</button>
            <a class="button secondary" href="{{ route('membership-plans.index') }}">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const daysCheckbox = document.getElementById('unit_days');
    const hoursCheckbox = document.getElementById('unit_hours');
    const durationInput = document.getElementById('duration_days');
    const durationLabel = document.getElementById('duration-label');
    const durationError = document.getElementById('duration-error');
    const form = document.querySelector('form');

    function updateDurationUnit(selectedUnit) {
        if (selectedUnit === 'days') {
            daysCheckbox.checked = true;
            hoursCheckbox.checked = false;
            durationLabel.textContent = 'Duration (days) *';
            durationInput.max = '3650';
            durationInput.value = Math.min(
                Math.max(Number(durationInput.value) || 1, 1),
                3650
            );
        } else if (selectedUnit === 'hours') {
            hoursCheckbox.checked = true;
            daysCheckbox.checked = false;
            durationLabel.textContent = 'Duration (hours) *';
            durationInput.max = '87600';
            durationInput.value = Math.min(
                Math.max(Number(durationInput.value) || 1, 1),
                87600
            );
        }
    }

    daysCheckbox.addEventListener('change', function () {
        if (this.checked) {
            updateDurationUnit('days');
        } else if (!hoursCheckbox.checked) {
            this.checked = true;
        }
    });

    hoursCheckbox.addEventListener('change', function () {
        if (this.checked) {
            updateDurationUnit('hours');
        } else if (!daysCheckbox.checked) {
            this.checked = true;
        }
    });

    form.addEventListener('submit', function (event) {
        if (!daysCheckbox.checked && !hoursCheckbox.checked) {
            event.preventDefault();
            durationError.style.display = 'block';
        } else {
            durationError.style.display = 'none';
        }
    });

    updateDurationUnit(hoursCheckbox.checked ? 'hours' : 'days');
</script>
</body>
</html>