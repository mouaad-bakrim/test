<?php

// File to update
$updateLogFile = 'update-log.txt';

// Loop through each day of a specific month as an example
$year = "2023";
for ($month = 9; $month <= 12; $month++) { // Loop through months
    for ($day = 1; $day <= date('t', mktime(0, 0, 0, $month, 1, $year)); $day++) { // Loop through days of the month

        // Determine a random number of commits for this day
        $numberOfCommitsToday = rand(1, 12); // Randomly between 1 and 12 commits

        for ($commitCount = 0; $commitCount < $numberOfCommitsToday; $commitCount++) {
            // Generate a random commit name
            $commitName = "Development Update " . bin2hex(random_bytes(3));

            // Specify the date for the commit
            $commitDate = sprintf("%s-%02d-%02d 12:%02d:00", $year, $month, $day, rand(0, 25)); // Adding randomness in hours:minutes as well

            // Append a new line with the current timestamp to update-log.txt
            file_put_contents($updateLogFile, "Update made at " . $commitDate . "\n", FILE_APPEND);

            // Prepare and execute git commands
            $commands = [
                "git add $updateLogFile",
                "git commit --date=\"$commitDate\" -m \"$commitName\"",
            ];

            foreach ($commands as $command) {
                // Output the command being executed
                echo "Executing: $command\n";

                // Execute command
                exec($command, $output, $return_var);
                if ($return_var !== 0) {
                    echo "Command failed with error code $return_var:\n";
                    foreach ($output as $line) {
                        echo $line . "\n";
                    }
                    // Consider whether to exit on failure based on your needs
                    break 3; // Exit all loops if a command fails
                }

                // Clear output after each command to handle new output
                $output = [];
            }
        }

        // Reset environment variables after each commit day (not strictly necessary here but kept for completeness)
        putenv("GIT_AUTHOR_DATE");
        putenv("GIT_COMMITTER_DATE");
    }
}

// Only attempt to push once after all commits are done
exec("git push", $output, $return_var);
if ($return_var === 0) {
    echo "Pushed all commits successfully.\n";
} else {
    echo "Failed to push commits.\n";
}

echo "Done";

?>