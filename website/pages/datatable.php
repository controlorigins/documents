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

<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="bi bi-download"></i> Export',
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<i class="bi bi-filetype-csv"></i> CSV'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bi bi-filetype-xlsx"></i> Excel'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bi bi-filetype-pdf"></i> PDF'
                        }
                    ]
                },
                {
                    extend: 'colvis',
                    text: '<i class="bi bi-eye"></i> Columns'
                }
            ]
        });
    });
</script>