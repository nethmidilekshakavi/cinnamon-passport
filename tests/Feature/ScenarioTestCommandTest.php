<?php

it('runs the RQ1 scenario test without errors', function () {
    $this->artisan('cinnamon:scenario-test')->assertSuccessful();
});
