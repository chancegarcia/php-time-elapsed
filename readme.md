# Introduction
This library is made to figure out elapsed time. Such as: "has 30 minutes elapsed so I can refresh this authentication token?"

# Installation
        composer require chancegarcia/php-time-elapsed
        
# Usage
        <?php
        
        ...
        
        $start = new \DateTime("2017-01-01");
        $end = clone $start;
        $end->modify("+1 hour");
        $diff = $start->diff($end);
        
        $service = new TimeElapsedService($diff);
        
        $service->hasMinutesElapsed(20); // true
        $service->hasTimeElapsed(1, 'minute'); // true
        $service->hasTimeElapsed(20, 'minutes'); // true
        $service->hasMinutesElapsed(90) // false
        $service->hasTimeElapsed(90, 'minutes'); // false
        