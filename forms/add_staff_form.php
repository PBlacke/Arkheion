<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-group">
        <label>Name</label>
        <div class="name-group">
            <div>
                <input type="text" name="first_name" placeholder="First Name" required>
            </div>
            <div>
                <input type="text" name="middle_name" placeholder="Middle Name">
            </div>
            <div>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>
            <div class="suffix-field">
                <input type="text" name="suffix" placeholder="Suffix">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Birthdate</label>
        <input type="date" name="birthdate" required>
    </div>

    <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" placeholder="Complete Address" required>
    </div>

    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Department</label>
        <select name="department" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo $department['id']; ?>">
                    <?php echo $department['department_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <button type="submit" class="w3-button w3-red">Add Staff</button>
    </div>
</form>