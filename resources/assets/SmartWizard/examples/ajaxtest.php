<?php
sleep(2);
$step_number = $_REQUEST["step_number"];
echo '<h2 class="StepTitle">Hello from Server! Step '.($step_number + 1).',</h2>
            <p>We have added a 2 sec sleep to feel the ajax loading, It would be faster otherwise</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, 
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>';