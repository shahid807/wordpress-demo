<?php
echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-striped align-middle">';
        echo '<thead class="table-dark"><tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Gender</th>
                <th scope="col">Country</th>
                <th scope="col">Profile Picture</th>
                <th scope="col">Created At</th>
            </tr></thead>';
        echo '<tbody>';

        foreach ($submissions as $submission) {
            echo '<tr>';
            echo '<td>' . esc_html($submission->id) . '</td>';
            echo '<td>' . esc_html($submission->name) . '</td>';
            echo '<td>' . esc_html($submission->email) . '</td>';
            echo '<td>' . esc_html($submission->phone) . '</td>';
            echo '<td>' . esc_html($submission->gender) . '</td>';
            echo '<td>' . esc_html($submission->country) . '</td>';
            echo '<td><img src="' . esc_url($submission->file_path) . '" alt="Profile Picture" class="img-thumbnail rounded-circle" style="width: 80px; height:80px; object-fit: cover;"></td>';
            echo '<td>' . esc_html($submission->created_at) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table></div>';