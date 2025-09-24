<form action="" method="POST" class="flex flex-col gap-2">
    <h2 class="text-lg font-bold">Submit Paper</h2>
    <div class="grid grid-cols-2 gap-2 w-full">
        <input type="text" name="title" class="input w-full" placeholder="Title" required>
        <input type="text" name="abstract" class="input w-full" placeholder="Abstract" required>
    </div>
    <div class="grid grid-cols-2 gap-2 w-full">
        <input type="text" name="authors" class="input w-full" placeholder="Authors" required>
        <input type="text" name="advisor" class="input w-full" placeholder="Advisor" required>
    </div>
    <div class="grid grid-cols-2 gap-2 w-full">
        <input type="date" name="date-of-submission" class="input w-full" placeholder="Date of Submission" value="<?php echo date('Y-m-d'); ?>" required>
        <select name="head_faculty_id" class="select w-full">
            <option value="" disabled selected>Department (optional)</option>
            <?php
            $departments = $db->getDepartments();

            foreach ($departments as $department) {
                echo "<option value=\"{$department['id']}\">" . htmlspecialchars($department['department_name']) . "</option>";
            }
            ?>
        </select>
    </div>
    <input type="text" name="institution" class="input w-full" placeholder="Institution" required>
    <div class="grid grid-cols-2 gap-2 w-full">
        <input type="text" name="document-type" class="input w-full" placeholder="Document Type" required>
        <input type="text" name="research-type" class="input w-full" placeholder="Research Type" required>
    </div>
    <div class="grid grid-cols-2 gap-2 w-full">
        <select name="published" class="select w-full">
            <option value="published" selected>Published</option>
            <option value="unpublished" selected>Unpublished</option>
        </select>
        <input type="text" name="keywords" class="input w-full" placeholder='Keywords (separate with ",")' required>
    </div>
    <button type="submit" class="btn btn-primary">Submit Paper</button>
</form>