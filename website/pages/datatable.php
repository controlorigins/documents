<?php
// Enhanced DataTable component with filtering, search, and export capabilities

// This can be included in data-analysis.php, project_list.php, and crud.php pages
?>
<div class="table-responsive">
    <table id="myTable" class="table table-striped table-hover table-bordered" style="width:100%">
        <thead class="table-dark">
            <tr>
                <?php foreach ($tableHeaders as $header): ?>
                <th><?php echo $header; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tableData as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                <td><?php echo $cell; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>