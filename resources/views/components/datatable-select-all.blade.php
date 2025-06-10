<script>
            function getSelectedIds() {
                        return $('.row-checkbox:checked').map(function () {
                            return this.value;
                        }).get();
            }
            $('#select-all').on('click', function() {
                        const isChecked = this.checked;
                        // Select/Deselect all visible row checkboxes
                        $('.row-checkbox').prop('checked', isChecked);

                        // Update the selectedClientIds map for the current page
                        table.rows({ page: 'current' }).data().each(function(row) {
                            if (isChecked) {
                                selectedClientIds[row.id] = true;
                            } else {
                                delete selectedClientIds[row.id];
                            }
                        });
            });
        </script>